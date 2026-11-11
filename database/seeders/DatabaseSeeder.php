<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AcademicSession;
use App\Models\ExamResult;
use App\Models\ExamSchedule;
use App\Models\Examination;
use App\Models\ExamType;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Notice;
use App\Models\ParentProfile;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\Timetable;
use App\Models\User;
use App\Support\GradeHelper;
use App\Support\NumberGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        SchoolSetting::query()->create([
            'name' => 'Sunrise Public School',
            'address' => '12 Lake View Road',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'postal_code' => '411001',
            'phone' => '020-2555-0101',
            'email' => 'office@sunrisepublic.test',
            'website' => 'https://sunrisepublic.test',
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'attendance_start_hour' => 8,
            'grading_system' => 'percentage',
            'fee_notes' => 'Fees are due by the 10th of each month.',
        ]);

        $session = AcademicSession::query()->create([
            'name' => '2025-26',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => true,
        ]);

        $admin = User::query()->create([
            'name' => 'School Admin',
            'email' => 'admin@school.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'phone' => '9000000001',
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Principal Mehta',
            'email' => 'principal@school.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Principal,
            'phone' => '9000000002',
            'email_verified_at' => now(),
        ]);

        $accountant = User::query()->create([
            'name' => 'Accountant Rao',
            'email' => 'accountant@school.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Accountant,
            'phone' => '9000000003',
            'email_verified_at' => now(),
        ]);

        $teacherUsers = collect([
            ['Anita Sharma', 'anita@school.test', 'Mathematics'],
            ['Rahul Verma', 'rahul@school.test', 'Science'],
            ['Neha Joshi', 'neha@school.test', 'English'],
        ])->map(function (array $row) {
            $user = User::query()->create([
                'name' => $row[0],
                'email' => $row[1],
                'password' => Hash::make('password'),
                'role' => UserRole::Teacher,
                'phone' => fake()->numerify('98########'),
                'email_verified_at' => now(),
            ]);

            Teacher::query()->create([
                'user_id' => $user->id,
                'employee_id' => NumberGenerator::employeeId(),
                'qualification' => 'M.Ed',
                'specialization' => $row[2],
                'joining_date' => '2022-06-01',
                'gender' => 'female',
                'address' => 'Pune',
            ]);

            return $user;
        });

        $classes = collect(['Class 6', 'Class 7', 'Class 8'])->map(function (string $name, int $index) {
            return SchoolClass::query()->create([
                'name' => $name,
                'numeric_name' => (string) (6 + $index),
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        });

        $sections = collect();
        foreach ($classes as $index => $class) {
            foreach (['A', 'B'] as $sectionName) {
                $sections->push(Section::query()->create([
                    'school_class_id' => $class->id,
                    'name' => $sectionName,
                    'class_teacher_id' => $teacherUsers[$index % $teacherUsers->count()]->id,
                    'capacity' => 40,
                ]));
            }
        }

        $subjects = collect([
            ['Mathematics', 'MATH'],
            ['Science', 'SCI'],
            ['English', 'ENG'],
            ['History', 'HIST'],
            ['Geography', 'GEO'],
        ])->map(fn (array $row) => Subject::query()->create([
            'name' => $row[0],
            'code' => $row[1],
            'type' => 'core',
            'is_active' => true,
        ]));

        foreach ($classes as $classIndex => $class) {
            foreach ($subjects as $subjectIndex => $subject) {
                DB::table('class_subject')->insert([
                    'school_class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacherUsers[$subjectIndex % $teacherUsers->count()]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $parentUser = User::query()->create([
            'name' => 'Parent Kapoor',
            'email' => 'parent@school.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Parent,
            'phone' => '9000000004',
            'email_verified_at' => now(),
        ]);

        $parent = ParentProfile::query()->create([
            'user_id' => $parentUser->id,
            'occupation' => 'Business',
            'relation' => 'father',
            'address' => 'Baner, Pune',
        ]);

        $students = collect();
        $roll = 1;
        foreach ($sections->take(3) as $section) {
            for ($i = 1; $i <= 5; $i++) {
                $user = User::query()->create([
                    'name' => fake()->name(),
                    'email' => "student{$roll}@school.test",
                    'password' => Hash::make('password'),
                    'role' => UserRole::Student,
                    'phone' => fake()->numerify('97########'),
                    'email_verified_at' => now(),
                ]);

                $student = Student::query()->create([
                    'user_id' => $user->id,
                    'admission_no' => NumberGenerator::admissionNumber(),
                    'admission_date' => now()->subMonths(random_int(1, 10))->toDateString(),
                    'academic_session_id' => $session->id,
                    'school_class_id' => $section->school_class_id,
                    'section_id' => $section->id,
                    'roll_no' => (string) $i,
                    'gender' => fake()->randomElement(['male', 'female']),
                    'date_of_birth' => now()->subYears(random_int(11, 14))->toDateString(),
                    'address' => 'Pune',
                    'city' => 'Pune',
                    'state' => 'Maharashtra',
                    'status' => 'active',
                ]);

                $students->push($student);
                $roll++;
            }
        }

        $parent->students()->attach($students->take(2)->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => ['is_primary' => $index === 0]]));

        $demoStudentUser = User::query()->create([
            'name' => 'Aarav Student',
            'email' => 'student@school.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Student,
            'phone' => '9000000005',
            'email_verified_at' => now(),
        ]);

        $demoStudent = Student::query()->create([
            'user_id' => $demoStudentUser->id,
            'admission_no' => NumberGenerator::admissionNumber(),
            'admission_date' => '2025-04-10',
            'academic_session_id' => $session->id,
            'school_class_id' => $classes[0]->id,
            'section_id' => $sections[0]->id,
            'roll_no' => '6',
            'gender' => 'male',
            'date_of_birth' => '2013-08-12',
            'address' => 'Pune',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'status' => 'active',
        ]);

        $parent->students()->attach($demoStudent->id, ['is_primary' => true]);
        $students->push($demoStudent);

        foreach ($students as $student) {
            StudentAttendance::query()->create([
                'student_id' => $student->id,
                'school_class_id' => $student->school_class_id,
                'section_id' => $student->section_id,
                'date' => today()->toDateString(),
                'status' => fake()->randomElement(['present', 'present', 'present', 'absent', 'leave']),
                'marked_by' => $teacherUsers[0]->id,
            ]);
        }

        foreach (Teacher::query()->get() as $teacher) {
            TeacherAttendance::query()->create([
                'teacher_id' => $teacher->id,
                'date' => today()->toDateString(),
                'status' => 'present',
                'marked_by' => $admin->id,
            ]);
        }

        $periods = collect([
            ['Period 1', '08:00', '08:45', 1],
            ['Period 2', '08:50', '09:35', 2],
            ['Period 3', '09:40', '10:25', 3],
            ['Period 4', '10:45', '11:30', 4],
        ])->map(fn (array $row) => Period::query()->create([
            'name' => $row[0],
            'start_time' => $row[1],
            'end_time' => $row[2],
            'sort_order' => $row[3],
        ]));

        foreach (range(1, 5) as $day) {
            foreach ($periods as $periodIndex => $period) {
                Timetable::query()->create([
                    'school_class_id' => $classes[0]->id,
                    'section_id' => $sections[0]->id,
                    'subject_id' => $subjects[$periodIndex % $subjects->count()]->id,
                    'teacher_id' => $teacherUsers[$periodIndex % $teacherUsers->count()]->id,
                    'period_id' => $period->id,
                    'day_of_week' => $day,
                    'academic_session_id' => $session->id,
                ]);
            }
        }

        $examType = ExamType::query()->create([
            'name' => 'Unit Test',
            'code' => 'UT',
            'description' => 'Periodic unit assessments',
        ]);

        $exam = Examination::query()->create([
            'name' => 'Unit Test 1',
            'exam_type_id' => $examType->id,
            'academic_session_id' => $session->id,
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'status' => 'scheduled',
        ]);

        $schedule = ExamSchedule::query()->create([
            'examination_id' => $exam->id,
            'school_class_id' => $classes[0]->id,
            'subject_id' => $subjects[0]->id,
            'exam_date' => now()->addDays(8)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'max_marks' => 50,
            'passing_marks' => 17,
            'room' => 'Hall A',
        ]);

        foreach ($students->where('school_class_id', $classes[0]->id) as $student) {
            $marks = random_int(20, 48);
            $percentage = ($marks / 50) * 100;
            ExamResult::query()->create([
                'exam_schedule_id' => $schedule->id,
                'student_id' => $student->id,
                'marks_obtained' => $marks,
                'grade' => GradeHelper::fromPercentage($percentage),
                'is_absent' => false,
                'entered_by' => $teacherUsers[0]->id,
            ]);
        }

        $homework = Homework::query()->create([
            'title' => 'Algebra Practice Worksheet',
            'description' => 'Complete exercises 1 to 10 from chapter 3.',
            'school_class_id' => $classes[0]->id,
            'section_id' => $sections[0]->id,
            'subject_id' => $subjects[0]->id,
            'teacher_id' => $teacherUsers[0]->id,
            'assigned_date' => today()->toDateString(),
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        foreach ($students->where('section_id', $sections[0]->id) as $student) {
            HomeworkSubmission::query()->create([
                'homework_id' => $homework->id,
                'student_id' => $student->id,
                'status' => 'pending',
            ]);
        }

        $tuition = FeeCategory::query()->create([
            'name' => 'Tuition Fee',
            'code' => 'TUITION',
            'description' => 'Monthly tuition',
            'is_active' => true,
        ]);

        $structure = FeeStructure::query()->create([
            'fee_category_id' => $tuition->id,
            'school_class_id' => $classes[0]->id,
            'academic_session_id' => $session->id,
            'amount' => 3500,
            'frequency' => 'monthly',
        ]);

        foreach ($students->take(8) as $index => $student) {
            $invoice = FeeInvoice::query()->create([
                'invoice_no' => NumberGenerator::invoiceNumber(),
                'student_id' => $student->id,
                'fee_structure_id' => $structure->id,
                'academic_session_id' => $session->id,
                'title' => 'Tuition Fee - '.now()->format('F Y'),
                'amount' => 3500,
                'paid_amount' => 0,
                'discount' => 0,
                'due_date' => now()->addDays(10)->toDateString(),
                'status' => 'unpaid',
            ]);

            if ($index < 3) {
                FeePayment::query()->create([
                    'receipt_no' => NumberGenerator::receiptNumber(),
                    'fee_invoice_id' => $invoice->id,
                    'student_id' => $student->id,
                    'amount' => 1500,
                    'payment_date' => today()->toDateString(),
                    'payment_method' => 'upi',
                    'received_by' => $accountant->id,
                ]);
                $invoice->paid_amount = 1500;
                $invoice->refreshStatus();
            }
        }

        $expenseCategory = ExpenseCategory::query()->create([
            'name' => 'Utilities',
            'code' => 'UTIL',
        ]);

        Expense::query()->create([
            'expense_category_id' => $expenseCategory->id,
            'title' => 'Electricity Bill',
            'description' => 'Monthly campus electricity',
            'amount' => 12500,
            'expense_date' => today()->toDateString(),
            'payment_method' => 'bank_transfer',
            'recorded_by' => $accountant->id,
        ]);

        Notice::query()->create([
            'title' => 'Welcome to the new academic year',
            'body' => "Classes for session {$session->name} are in progress. Parents are requested to clear pending fees by the due date.",
            'type' => 'academic',
            'audience' => 'all',
            'is_important' => true,
            'publish_date' => today()->toDateString(),
            'created_by' => $admin->id,
        ]);

        Notice::query()->create([
            'title' => 'Sports Day Circular',
            'body' => 'Annual sports day will be held next month. Consent forms will be shared with parents.',
            'type' => 'event',
            'audience' => 'parents',
            'is_important' => false,
            'publish_date' => today()->toDateString(),
            'created_by' => $admin->id,
        ]);
    }
}
