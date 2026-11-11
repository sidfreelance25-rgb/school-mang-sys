<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeworkController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $homeworks = Homework::query()
            ->with(['schoolClass', 'section', 'subject', 'teacher'])
            ->when($user->isTeacher(), fn ($q) => $q->where('teacher_id', $user->id))
            ->when($user->isStudent() && $user->student, function ($q) use ($user): void {
                $q->where('school_class_id', $user->student->school_class_id)
                    ->where(function ($inner) use ($user): void {
                        $inner->whereNull('section_id')->orWhere('section_id', $user->student->section_id);
                    });
            })
            ->when($user->isParent() && $user->parentProfile, function ($q) use ($user): void {
                $children = $user->parentProfile->students()->get();
                $classIds = $children->pluck('school_class_id');
                $sectionIds = $children->pluck('section_id');
                $q->whereIn('school_class_id', $classIds)
                    ->where(function ($inner) use ($sectionIds): void {
                        $inner->whereNull('section_id')->orWhereIn('section_id', $sectionIds);
                    });
            })
            ->latest()
            ->paginate(15);

        return view('homework.index', [
            'homeworks' => $homeworks,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('schoolClass')->orderBy('name')->get(),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'assigned_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:assigned_date'],
            'attachment' => ['nullable', 'file', 'max:4096'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/homework', 'public');
        }

        $homework = Homework::query()->create([
            ...$data,
            'teacher_id' => $request->user()->id,
            'attachment' => $path,
        ]);

        $students = Student::query()
            ->active()
            ->where('school_class_id', $homework->school_class_id)
            ->when($homework->section_id, fn ($q) => $q->where('section_id', $homework->section_id))
            ->get();

        foreach ($students as $student) {
            HomeworkSubmission::query()->firstOrCreate(
                ['homework_id' => $homework->id, 'student_id' => $student->id],
                ['status' => 'pending']
            );
        }

        return redirect()->route('homework.index')->with('success', 'Homework assigned.');
    }

    public function show(Homework $homework): View
    {
        $homework->load(['schoolClass', 'section', 'subject', 'teacher', 'submissions.student.user']);

        return view('homework.show', compact('homework'));
    }

    public function updateSubmission(Request $request, HomeworkSubmission $submission): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,submitted,late,reviewed'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:4096'],
        ]);

        $path = $submission->attachment;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/homework', 'public');
        }

        $submission->update([
            'status' => $data['status'],
            'remarks' => $data['remarks'] ?? null,
            'attachment' => $path,
            'submitted_at' => in_array($data['status'], ['submitted', 'late', 'reviewed'], true) ? now() : $submission->submitted_at,
        ]);

        return back()->with('success', 'Submission updated.');
    }
}
