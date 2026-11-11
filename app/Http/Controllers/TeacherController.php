<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Support\NumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $teachers = Teacher::query()
            ->with('user')
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->where('employee_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('teachers.create', [
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(),
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
                'role' => UserRole::Teacher,
                'email_verified_at' => now(),
            ]);

            Teacher::query()->create([
                'user_id' => $user->id,
                'employee_id' => $data['employee_id'] ?: NumberGenerator::employeeId(),
                'qualification' => $data['qualification'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'joining_date' => $data['joining_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'salary' => $data['salary'] ?? null,
            ]);

            if (! empty($data['subject_ids']) && ! empty($data['class_ids'])) {
                foreach ($data['class_ids'] as $classId) {
                    foreach ($data['subject_ids'] as $subjectId) {
                        DB::table('class_subject')->updateOrInsert(
                            ['school_class_id' => $classId, 'subject_id' => $subjectId],
                            ['teacher_id' => $user->id, 'updated_at' => now(), 'created_at' => now()]
                        );
                    }
                }
            }
        });

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['user', 'attendances' => fn ($q) => $q->latest('date')->limit(30)]);

        $assignments = DB::table('class_subject')
            ->join('school_classes', 'school_classes.id', '=', 'class_subject.school_class_id')
            ->join('subjects', 'subjects.id', '=', 'class_subject.subject_id')
            ->where('class_subject.teacher_id', $teacher->user_id)
            ->select('school_classes.name as class_name', 'subjects.name as subject_name')
            ->get();

        return view('teachers.show', compact('teacher', 'assignments'));
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.edit', [
            'teacher' => $teacher,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $data = $this->validated($request, $teacher);

        DB::transaction(function () use ($data, $teacher): void {
            $teacher->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            if (! empty($data['password'])) {
                $teacher->user->update(['password' => Hash::make($data['password'])]);
            }

            $teacher->update([
                'qualification' => $data['qualification'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'joining_date' => $data['joining_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'salary' => $data['salary'] ?? null,
            ]);
        });

        return redirect()->route('teachers.show', $teacher)->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->user()->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher removed successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Teacher $teacher = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher?->user_id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('teachers', 'employee_id')->ignore($teacher?->id)],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:30'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['exists:school_classes,id'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);
    }
}
