<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Examination;
use App\Models\ExamResult;
use App\Models\ExamSchedule;
use App\Models\ExamType;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Support\GradeHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExaminationController extends Controller
{
    public function index(): View
    {
        return view('examinations.index', [
            'examinations' => Examination::query()->with(['examType', 'academicSession'])->latest()->paginate(15),
            'examTypes' => ExamType::query()->orderBy('name')->get(),
            'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function storeType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:exam_types,code'],
            'description' => ['nullable', 'string'],
        ]);

        ExamType::query()->create($data);

        return back()->with('success', 'Exam type created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ]);

        Examination::query()->create($data + ['status' => 'scheduled']);

        return back()->with('success', 'Examination created.');
    }

    public function show(Examination $examination): View
    {
        $examination->load(['examType', 'academicSession', 'schedules.subject', 'schedules.schoolClass']);

        return view('examinations.show', [
            'examination' => $examination,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeSchedule(Request $request, Examination $examination): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'max_marks' => ['required', 'numeric', 'min:1'],
            'passing_marks' => ['required', 'numeric', 'min:0'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        $examination->schedules()->updateOrCreate(
            [
                'school_class_id' => $data['school_class_id'],
                'subject_id' => $data['subject_id'],
            ],
            $data
        );

        return back()->with('success', 'Exam schedule saved.');
    }

    public function marks(ExamSchedule $schedule): View
    {
        $schedule->load(['examination', 'schoolClass', 'subject']);

        $students = Student::query()
            ->active()
            ->with('user')
            ->where('school_class_id', $schedule->school_class_id)
            ->orderBy('roll_no')
            ->get();

        $results = ExamResult::query()
            ->where('exam_schedule_id', $schedule->id)
            ->get()
            ->keyBy('student_id');

        return view('examinations.marks', compact('schedule', 'students', 'results'));
    }

    public function storeMarks(Request $request, ExamSchedule $schedule): RedirectResponse
    {
        $data = $request->validate([
            'marks' => ['required', 'array'],
            'marks.*.marks_obtained' => ['nullable', 'numeric', 'min:0'],
            'marks.*.is_absent' => ['nullable', 'boolean'],
            'marks.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $schedule, $request): void {
            foreach ($data['marks'] as $studentId => $row) {
                $absent = (bool) ($row['is_absent'] ?? false);
                $marks = $absent ? null : ($row['marks_obtained'] ?? null);
                $percentage = ($marks !== null && (float) $schedule->max_marks > 0)
                    ? ((float) $marks / (float) $schedule->max_marks) * 100
                    : null;

                ExamResult::query()->updateOrCreate(
                    [
                        'exam_schedule_id' => $schedule->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'grade' => GradeHelper::fromPercentage($percentage),
                        'is_absent' => $absent,
                        'remarks' => $row['remarks'] ?? null,
                        'entered_by' => $request->user()->id,
                    ]
                );
            }
        });

        return back()->with('success', 'Marks saved successfully.');
    }

    public function reportCard(Student $student, Examination $examination): View
    {
        $student->load(['user', 'schoolClass', 'section']);

        $results = ExamResult::query()
            ->with(['examSchedule.subject'])
            ->where('student_id', $student->id)
            ->whereHas('examSchedule', fn ($q) => $q->where('examination_id', $examination->id))
            ->get();

        $totalMax = $results->sum(fn (ExamResult $result) => (float) $result->examSchedule->max_marks);
        $totalObtained = $results->where('is_absent', false)->sum(fn (ExamResult $result) => (float) $result->marks_obtained);
        $percentage = $totalMax > 0 ? ($totalObtained / $totalMax) * 100 : null;

        return view('examinations.report-card', [
            'student' => $student,
            'examination' => $examination->load('examType'),
            'results' => $results,
            'totalMax' => $totalMax,
            'totalObtained' => $totalObtained,
            'percentage' => $percentage,
            'grade' => GradeHelper::fromPercentage($percentage),
        ]);
    }
}
