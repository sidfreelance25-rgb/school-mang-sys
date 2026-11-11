<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AcademicSession;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(Request $request): View
    {
        $sectionId = $request->integer('section_id') ?: null;
        $session = AcademicSession::current();

        $entries = collect();
        if ($sectionId && $session) {
            $entries = Timetable::query()
                ->with(['subject', 'teacher', 'period'])
                ->where('section_id', $sectionId)
                ->where('academic_session_id', $session->id)
                ->get()
                ->groupBy(fn (Timetable $item) => $item->day_of_week.'-'.$item->period_id);
        }

        return view('timetable.index', [
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('schoolClass')->orderBy('name')->get(),
            'periods' => Period::query()->orderBy('sort_order')->get(),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(),
            'teachers' => User::query()->where('role', UserRole::Teacher)->orderBy('name')->get(),
            'entries' => $entries,
            'sectionId' => $sectionId,
            'session' => $session,
            'days' => range(1, 6),
        ]);
    }

    public function storePeriod(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Period::query()->create($data);

        return back()->with('success', 'Period created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'period_id' => ['required', 'exists:periods,id'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
        ]);

        $teacherConflict = Timetable::query()
            ->where('teacher_id', $data['teacher_id'])
            ->where('period_id', $data['period_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('academic_session_id', $data['academic_session_id'])
            ->exists();

        if ($teacherConflict) {
            throw ValidationException::withMessages([
                'teacher_id' => 'This teacher already has a class in the selected period.',
            ]);
        }

        Timetable::query()->updateOrCreate(
            [
                'section_id' => $data['section_id'],
                'period_id' => $data['period_id'],
                'day_of_week' => $data['day_of_week'],
                'academic_session_id' => $data['academic_session_id'],
            ],
            [
                'school_class_id' => $data['school_class_id'],
                'subject_id' => $data['subject_id'],
                'teacher_id' => $data['teacher_id'],
            ]
        );

        return back()->with('success', 'Timetable slot saved.');
    }

    public function destroy(Timetable $timetable): RedirectResponse
    {
        $timetable->delete();

        return back()->with('success', 'Timetable slot removed.');
    }
}
