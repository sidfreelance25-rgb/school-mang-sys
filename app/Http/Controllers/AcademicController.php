<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicController extends Controller
{
    public function sessions(): View
    {
        return view('academic.sessions', [
            'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function storeSession(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['is_current'])) {
            AcademicSession::query()->update(['is_current' => false]);
        }

        AcademicSession::query()->create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_current' => (bool) ($data['is_current'] ?? false),
        ]);

        return back()->with('success', 'Academic session saved.');
    }

    public function setCurrentSession(AcademicSession $session): RedirectResponse
    {
        AcademicSession::query()->update(['is_current' => false]);
        $session->update(['is_current' => true]);

        return back()->with('success', 'Current academic session updated.');
    }

    public function classes(): View
    {
        return view('academic.classes', [
            'classes' => SchoolClass::query()->with('sections.classTeacher')->orderBy('sort_order')->get(),
            'teachers' => User::query()->where('role', UserRole::Teacher)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeClass(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'numeric_name' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        SchoolClass::query()->create([
            'name' => $data['name'],
            'numeric_name' => $data['numeric_name'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Class created.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'name' => ['required', 'string', 'max:20'],
            'class_teacher_id' => ['nullable', 'exists:users,id'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        Section::query()->create([
            'school_class_id' => $data['school_class_id'],
            'name' => $data['name'],
            'class_teacher_id' => $data['class_teacher_id'] ?? null,
            'capacity' => $data['capacity'] ?? 40,
        ]);

        return back()->with('success', 'Section created.');
    }

    public function subjects(): View
    {
        return view('academic.subjects', [
            'subjects' => Subject::query()->orderBy('name')->get(),
            'classes' => SchoolClass::query()->with('subjects')->orderBy('sort_order')->get(),
            'teachers' => User::query()->where('role', UserRole::Teacher)->orderBy('name')->get(),
        ]);
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'type' => ['required', Rule::in(['core', 'elective', 'optional'])],
        ]);

        Subject::query()->create($data + ['is_active' => true]);

        return back()->with('success', 'Subject created.');
    }

    public function assignSubject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        DB::table('class_subject')->updateOrInsert(
            [
                'school_class_id' => $data['school_class_id'],
                'subject_id' => $data['subject_id'],
            ],
            [
                'teacher_id' => $data['teacher_id'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Subject assigned to class.');
    }
}
