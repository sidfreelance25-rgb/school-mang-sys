<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Expense;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function admissions(Request $request): View
    {
        $students = Student::query()
            ->with(['user', 'schoolClass', 'section', 'academicSession'])
            ->when($request->date('from'), fn ($q, $from) => $q->whereDate('admission_date', '>=', $from))
            ->when($request->date('to'), fn ($q, $to) => $q->whereDate('admission_date', '<=', $to))
            ->when($request->integer('class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->latest('admission_date')
            ->paginate(30)
            ->withQueryString();

        return view('reports.admissions', [
            'students' => $students,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function attendance(Request $request): View
    {
        $from = $request->date('from')?->toDateString() ?? now()->startOfMonth()->toDateString();
        $to = $request->date('to')?->toDateString() ?? today()->toDateString();

        return view('reports.attendance', [
            'studentRecords' => StudentAttendance::query()->with(['student.user', 'schoolClass'])->whereBetween('date', [$from, $to])->latest('date')->limit(100)->get(),
            'teacherRecords' => TeacherAttendance::query()->with('teacher.user')->whereBetween('date', [$from, $to])->latest('date')->limit(100)->get(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function fees(Request $request): View
    {
        $payments = FeePayment::query()
            ->with(['student.user', 'feeInvoice'])
            ->when($request->date('from'), fn ($q, $from) => $q->whereDate('payment_date', '>=', $from))
            ->when($request->date('to'), fn ($q, $to) => $q->whereDate('payment_date', '<=', $to))
            ->latest('payment_date')
            ->paginate(30)
            ->withQueryString();

        $pending = FeeInvoice::query()
            ->with('student.user')
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date')
            ->get();

        return view('reports.fees', [
            'payments' => $payments,
            'pending' => $pending,
            'collected' => FeePayment::query()->sum('amount'),
            'outstanding' => FeeInvoice::query()->whereIn('status', ['unpaid', 'partial'])->get()->sum(fn (FeeInvoice $invoice) => $invoice->balance),
        ]);
    }

    public function examinations(): View
    {
        $results = ExamResult::query()
            ->with(['student.user', 'examSchedule.subject', 'examSchedule.examination', 'examSchedule.schoolClass'])
            ->latest()
            ->limit(100)
            ->get();

        return view('reports.examinations', compact('results'));
    }

    public function expenses(Request $request): View
    {
        $expenses = Expense::query()
            ->with('category')
            ->when($request->date('from'), fn ($q, $from) => $q->whereDate('expense_date', '>=', $from))
            ->when($request->date('to'), fn ($q, $to) => $q->whereDate('expense_date', '<=', $to))
            ->latest('expense_date')
            ->paginate(30)
            ->withQueryString();

        return view('reports.expenses', [
            'expenses' => $expenses,
            'total' => $expenses->sum('amount'),
        ]);
    }

    public function classStudents(Request $request): View
    {
        $classId = $request->integer('class_id') ?: null;

        $students = Student::query()
            ->active()
            ->with(['user', 'section'])
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->orderBy('roll_no')
            ->get();

        return view('reports.class-students', [
            'students' => $students,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'classId' => $classId,
        ]);
    }
}
