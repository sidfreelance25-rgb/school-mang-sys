<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParentController extends Controller
{
    public function index(Request $request): View
    {
        $parents = ParentProfile::query()
            ->with(['user', 'students.user'])
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->whereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('parents.index', compact('parents'));
    }

    public function create(): View
    {
        return view('parents.create', [
            'students' => Student::query()->active()->with('user')->orderBy('admission_no')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data): void {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password'] ?? 'password'),
                'role' => UserRole::Parent,
                'email_verified_at' => now(),
            ]);

            $parent = ParentProfile::query()->create([
                'user_id' => $user->id,
                'occupation' => $data['occupation'] ?? null,
                'relation' => $data['relation'] ?? 'guardian',
                'address' => $data['address'] ?? null,
                'alternate_phone' => $data['alternate_phone'] ?? null,
            ]);

            if (! empty($data['student_ids'])) {
                $sync = [];
                foreach ($data['student_ids'] as $index => $studentId) {
                    $sync[$studentId] = ['is_primary' => $index === 0];
                }
                $parent->students()->sync($sync);
            }
        });

        return redirect()->route('parents.index')->with('success', 'Parent profile created successfully.');
    }

    public function show(ParentProfile $parent): View
    {
        $parent->load(['user', 'students.user', 'students.schoolClass', 'students.section', 'students.feeInvoices', 'students.attendances' => fn ($q) => $q->latest('date')->limit(10)]);

        return view('parents.show', compact('parent'));
    }

    public function edit(ParentProfile $parent): View
    {
        $parent->load(['user', 'students']);

        return view('parents.edit', [
            'parent' => $parent,
            'students' => Student::query()->active()->with('user')->orderBy('admission_no')->get(),
        ]);
    }

    public function update(Request $request, ParentProfile $parent): RedirectResponse
    {
        $data = $this->validated($request, $parent);

        DB::transaction(function () use ($data, $parent): void {
            $parent->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            if (! empty($data['password'])) {
                $parent->user->update(['password' => Hash::make($data['password'])]);
            }

            $parent->update([
                'occupation' => $data['occupation'] ?? null,
                'relation' => $data['relation'] ?? 'guardian',
                'address' => $data['address'] ?? null,
                'alternate_phone' => $data['alternate_phone'] ?? null,
            ]);

            if (isset($data['student_ids'])) {
                $sync = [];
                foreach ($data['student_ids'] as $index => $studentId) {
                    $sync[$studentId] = ['is_primary' => $index === 0];
                }
                $parent->students()->sync($sync);
            }
        });

        return redirect()->route('parents.show', $parent)->with('success', 'Parent updated successfully.');
    }

    public function destroy(ParentProfile $parent): RedirectResponse
    {
        $parent->user()->delete();

        return redirect()->route('parents.index')->with('success', 'Parent removed successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ParentProfile $parent = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($parent?->user_id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'relation' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ]);
    }
}
