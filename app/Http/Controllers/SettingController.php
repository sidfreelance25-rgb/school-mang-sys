<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'school' => SchoolSetting::current(),
            'users' => User::query()->orderBy('role')->orderBy('name')->paginate(20),
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'string', 'max:150'],
            'working_days' => ['nullable', 'array'],
            'working_days.*' => ['string'],
            'attendance_start_hour' => ['nullable', 'integer', 'min:0', 'max:23'],
            'grading_system' => ['required', Rule::in(['percentage', 'gpa'])],
            'fee_notes' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $school = SchoolSetting::current();
        $logo = $school->logo;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('uploads/school', 'public');
        }

        $school->update([
            ...$data,
            'logo' => $logo,
            'working_days' => $data['working_days'] ?? [],
        ]);

        return back()->with('success', 'School settings updated.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(UserRole::values())],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::query()->create([
            ...$data,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'User created.');
    }

    public function toggleUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'User status updated.');
    }
}
