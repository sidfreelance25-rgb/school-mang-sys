<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function students(Request $request): View
    {
        $date = $request->date('date')?->toDateString() ?? today()->toDateString();
        $classId = $request->integer('class_id') ?: null;
        $sectionId = $request->integer('section_id') ?: null;

        $students = collect();
        $existing = collect();

        if ($classId && $sectionId) {
            $students = Student::query()
                ->active()
                ->with('user')
                ->where('school_class_id', $classId)
                ->where('section_id', $sectionId)
                ->orderBy('roll_no')
                ->get();

            $existing = StudentAttendance::query()
                ->whereDate('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.students', [
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('schoolClass')->orderBy('name')->get(),
            'students' => $students,
            'existing' => $existing,
            'date' => $date,
            'classId' => $classId,
            'sectionId' => $sectionId,
        ]);
    }

    public function storeStudents(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', Rule::in(['present', 'absent', 'leave'])],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $request): void {
            foreach ($data['attendance'] as $studentId => $row) {
                StudentAttendance::query()->updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $data['date'],
                    ],
                    [
                        'school_class_id' => $data['school_class_id'],
                        'section_id' => $data['section_id'],
                        'status' => $row['status'],
                        'remarks' => $row['remarks'] ?? null,
                        'marked_by' => $request->user()->id,
                    ]
                );
            }
        });

        return back()->with('success', 'Student attendance saved.');
    }

    public function teachers(Request $request): View
    {
        $date = $request->date('date')?->toDateString() ?? today()->toDateString();
        $teachers = Teacher::query()->with('user')->orderBy('employee_id')->get();
        $existing = TeacherAttendance::query()
            ->whereDate('date', $date)
            ->get()
            ->keyBy('teacher_id');

        return view('attendance.teachers', compact('teachers', 'existing', 'date'));
    }

    public function storeTeachers(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', Rule::in(['present', 'absent', 'leave'])],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data['attendance'] as $teacherId => $row) {
            TeacherAttendance::query()->updateOrCreate(
                [
                    'teacher_id' => $teacherId,
                    'date' => $data['date'],
                ],
                [
                    'status' => $row['status'],
                    'remarks' => $row['remarks'] ?? null,
                    'marked_by' => $request->user()->id,
                ]
            );
        }

        return back()->with('success', 'Teacher attendance saved.');
    }

    public function report(Request $request): View
    {
        $from = $request->date('from')?->toDateString() ?? now()->startOfMonth()->toDateString();
        $to = $request->date('to')?->toDateString() ?? today()->toDateString();

        $studentStats = StudentAttendance::query()
            ->selectRaw('status, COUNT(*) as total')
            ->whereBetween('date', [$from, $to])
            ->groupBy('status')
            ->pluck('total', 'status');

        $records = StudentAttendance::query()
            ->with(['student.user', 'schoolClass', 'section'])
            ->when($request->integer('class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->whereBetween('date', [$from, $to])
            ->latest('date')
            ->paginate(25)
            ->withQueryString();

        return view('attendance.report', [
            'records' => $records,
            'studentStats' => $studentStats,
            'from' => $from,
            'to' => $to,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
        ]);
    }
}
