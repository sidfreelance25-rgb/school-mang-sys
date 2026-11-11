<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Examination;
use App\Models\Expense;
use App\Models\FeeInvoice;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $school = SchoolSetting::current();
        $notices = Notice::query()->published()->latest('publish_date')->limit(5)->get();

        $stats = [
            'students' => Student::query()->active()->count(),
            'teachers' => Teacher::query()->count(),
            'classes' => SchoolClass::query()->where('is_active', true)->count(),
            'sections' => Section::query()->count(),
            'pending_fees' => FeeInvoice::query()->whereIn('status', ['unpaid', 'partial'])->sum('amount')
                - FeeInvoice::query()->whereIn('status', ['unpaid', 'partial'])->sum('paid_amount')
                - FeeInvoice::query()->whereIn('status', ['unpaid', 'partial'])->sum('discount'),
            'today_present' => StudentAttendance::query()->whereDate('date', today())->where('status', 'present')->count(),
            'today_absent' => StudentAttendance::query()->whereDate('date', today())->where('status', 'absent')->count(),
            'upcoming_exams' => Examination::query()->whereDate('start_date', '>=', today())->orderBy('start_date')->limit(5)->get(),
            'expenses_month' => Expense::query()->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'staff' => User::query()->whereIn('role', [UserRole::Teacher, UserRole::Accountant, UserRole::Admin])->where('is_active', true)->count(),
        ];

        $roleData = match ($user->role) {
            UserRole::Teacher => [
                'homeworks' => Homework::query()->where('teacher_id', $user->id)->latest()->limit(5)->get(),
            ],
            UserRole::Student => [
                'student' => $user->student?->load([
                    'schoolClass',
                    'section',
                    'feeInvoices',
                    'attendances' => fn ($q) => $q->latest('date')->limit(10),
                ]),
                'homeworks' => $user->student
                    ? Homework::query()
                        ->where('school_class_id', $user->student->school_class_id)
                        ->where(function ($q) use ($user): void {
                            $q->whereNull('section_id')->orWhere('section_id', $user->student->section_id);
                        })
                        ->latest()
                        ->limit(5)
                        ->get()
                    : collect(),
            ],
            UserRole::Parent => [
                'children' => $user->parentProfile?->students()->with(['schoolClass', 'section', 'user'])->get() ?? collect(),
            ],
            UserRole::Accountant => [
                'unpaid_invoices' => FeeInvoice::query()->with('student.user')->whereIn('status', ['unpaid', 'partial'])->latest()->limit(8)->get(),
            ],
            default => [],
        };

        return view('dashboard', [
            'school' => $school,
            'stats' => $stats,
            'notices' => $notices,
            'roleData' => $roleData,
        ]);
    }
}
