<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('exam_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('scheduled');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('max_marks', 8, 2)->default(100);
            $table->decimal('passing_marks', 8, 2)->default(33);
            $table->string('room')->nullable();
            $table->timestamps();
            $table->unique(['examination_id', 'school_class_id', 'subject_id'], 'exam_schedule_unique');
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->string('grade')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->text('remarks')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['exam_schedule_id', 'student_id']);
        });

        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('assigned_date');
            $table->date('due_date');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_id')->constrained('homeworks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->string('attachment')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['homework_id', 'student_id']);
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type')->default('general');
            $table->string('audience')->default('all');
            $table->boolean('is_important')->default(false);
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homeworks');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('examinations');
        Schema::dropIfExists('exam_types');
    }
};
