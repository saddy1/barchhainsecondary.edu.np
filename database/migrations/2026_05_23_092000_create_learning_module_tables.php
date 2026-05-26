<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('learning_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_class_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('code', 40)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('learning_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('material_path')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('learning_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedTinyInteger('pass_percent')->default(50);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        Schema::create('learning_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->unsignedInteger('marks')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_question_id')->constrained()->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'learning_course_id', 'learning_lesson_id'], 'learning_progress_unique');
        });

        Schema::create('learning_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_quiz_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_marks')->default(0);
            $table->unsignedTinyInteger('percent')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('learning_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('learning_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['note', 'syllabus', 'old-question', 'practice-material'])->default('note');
            $table->string('file_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        DB::table('module_settings')->updateOrInsert(
            ['key' => 'learning'],
            [
                'label' => 'E-Learning',
                'description' => 'Courses, lessons, quizzes, scores and student learning resources',
                'group' => 'ERP',
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_resources');
        Schema::dropIfExists('learning_quiz_attempts');
        Schema::dropIfExists('learning_progress');
        Schema::dropIfExists('learning_question_options');
        Schema::dropIfExists('learning_questions');
        Schema::dropIfExists('learning_quizzes');
        Schema::dropIfExists('learning_lessons');
        Schema::dropIfExists('learning_courses');
        Schema::dropIfExists('learning_subjects');
        Schema::dropIfExists('learning_classes');

        DB::table('module_settings')->where('key', 'learning')->delete();
    }
};
