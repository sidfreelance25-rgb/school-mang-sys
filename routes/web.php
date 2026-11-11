<?php

use App\Http\Controllers\AcademicController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin,principal')->group(function () {
        Route::get('/students/promote', [StudentController::class, 'promoteForm'])->name('students.promote');
        Route::post('/students/promote', [StudentController::class, 'promote'])->name('students.promote.store');
        Route::resource('students', StudentController::class)->except(['show']);
        Route::resource('teachers', TeacherController::class);
        Route::resource('parents', ParentController::class);

        Route::get('/academic/sessions', [AcademicController::class, 'sessions'])->name('academic.sessions');
        Route::post('/academic/sessions', [AcademicController::class, 'storeSession'])->name('academic.sessions.store');
        Route::post('/academic/sessions/{session}/current', [AcademicController::class, 'setCurrentSession'])->name('academic.sessions.current');
        Route::get('/academic/classes', [AcademicController::class, 'classes'])->name('academic.classes');
        Route::post('/academic/classes', [AcademicController::class, 'storeClass'])->name('academic.classes.store');
        Route::post('/academic/sections', [AcademicController::class, 'storeSection'])->name('academic.sections.store');
        Route::get('/academic/subjects', [AcademicController::class, 'subjects'])->name('academic.subjects');
        Route::post('/academic/subjects', [AcademicController::class, 'storeSubject'])->name('academic.subjects.store');
        Route::post('/academic/subjects/assign', [AcademicController::class, 'assignSubject'])->name('academic.subjects.assign');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/users', [SettingController::class, 'storeUser'])->name('settings.users.store');
        Route::post('/settings/users/{user}/toggle', [SettingController::class, 'toggleUser'])->name('settings.users.toggle');
    });

    Route::middleware('role:admin,principal,teacher')->group(function () {
        Route::get('/attendance/students', [AttendanceController::class, 'students'])->name('attendance.students');
        Route::post('/attendance/students', [AttendanceController::class, 'storeStudents'])->name('attendance.students.store');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

        Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
        Route::post('/timetable/periods', [TimetableController::class, 'storePeriod'])->name('timetable.periods.store');
        Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('/timetable/{timetable}', [TimetableController::class, 'destroy'])->name('timetable.destroy');

        Route::get('/examinations', [ExaminationController::class, 'index'])->name('examinations.index');
        Route::post('/examinations/types', [ExaminationController::class, 'storeType'])->name('examinations.types.store');
        Route::post('/examinations', [ExaminationController::class, 'store'])->name('examinations.store');
        Route::get('/examinations/{examination}', [ExaminationController::class, 'show'])->name('examinations.show');
        Route::post('/examinations/{examination}/schedules', [ExaminationController::class, 'storeSchedule'])->name('examinations.schedules.store');
        Route::get('/exam-schedules/{schedule}/marks', [ExaminationController::class, 'marks'])->name('examinations.marks');
        Route::post('/exam-schedules/{schedule}/marks', [ExaminationController::class, 'storeMarks'])->name('examinations.marks.store');
    });

    Route::middleware('role:admin,principal')->group(function () {
        Route::get('/attendance/teachers', [AttendanceController::class, 'teachers'])->name('attendance.teachers');
        Route::post('/attendance/teachers', [AttendanceController::class, 'storeTeachers'])->name('attendance.teachers.store');
    });

    Route::middleware('role:admin,principal,teacher,student,parent')->group(function () {
        Route::get('/homework', [HomeworkController::class, 'index'])->name('homework.index');
        Route::get('/homework/{homework}', [HomeworkController::class, 'show'])->name('homework.show');
    });

    Route::middleware('role:admin,principal,teacher')->group(function () {
        Route::post('/homework', [HomeworkController::class, 'store'])->name('homework.store');
        Route::patch('/homework-submissions/{submission}', [HomeworkController::class, 'updateSubmission'])->name('homework.submissions.update');
    });

    Route::middleware('role:admin,accountant')->group(function () {
        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
        Route::post('/fees/categories', [FeeController::class, 'storeCategory'])->name('fees.categories.store');
        Route::post('/fees/structures', [FeeController::class, 'storeStructure'])->name('fees.structures.store');
        Route::post('/fees/invoices', [FeeController::class, 'storeInvoice'])->name('fees.invoices.store');
        Route::get('/fees/{fee}', [FeeController::class, 'show'])->name('fees.show');
        Route::post('/fees/{fee}/payments', [FeeController::class, 'storePayment'])->name('fees.payments.store');
        Route::get('/fee-payments/{payment}/receipt', [FeeController::class, 'receipt'])->name('fees.receipt');

        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    Route::middleware('role:admin,principal,accountant,student,parent')->group(function () {
        Route::get('/fees/{fee}/view', [FeeController::class, 'show'])->name('fees.view');
        Route::get('/fee-payments/{payment}/print', [FeeController::class, 'receipt'])->name('fees.print');
    });

    Route::middleware('role:admin,principal,teacher,student,parent')->group(function () {
        Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
    });

    Route::middleware('role:admin,principal')->group(function () {
        Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
        Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    });

    Route::middleware('role:admin,principal,teacher')->group(function () {
        Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
    });

    Route::middleware('role:admin,principal')->group(function () {
        Route::post('/leaves/{leave}/review', [LeaveRequestController::class, 'review'])->name('leaves.review');
    });

    Route::middleware('role:admin,principal,accountant')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/admissions', [ReportController::class, 'admissions'])->name('reports.admissions');
        Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/fees', [ReportController::class, 'fees'])->name('reports.fees');
        Route::get('/reports/examinations', [ReportController::class, 'examinations'])->name('reports.examinations');
        Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
        Route::get('/reports/class-students', [ReportController::class, 'classStudents'])->name('reports.class-students');
    });

    Route::get('/students/{student}/report-cards/{examination}', [ExaminationController::class, 'reportCard'])
        ->middleware('role:admin,principal,teacher,student,parent')
        ->name('examinations.report-card');

    Route::get('/students/{student}', [StudentController::class, 'show'])
        ->middleware('role:admin,principal,teacher,accountant,student,parent')
        ->name('students.show');
});

require __DIR__.'/auth.php';
