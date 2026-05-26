<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('learning_lessons', function (Blueprint $table) {
            $table->foreignId('learning_chapter_id')->nullable()->after('learning_course_id')
                  ->constrained('learning_chapters')->nullOnDelete();
            $table->enum('type', ['video', 'audio', 'text'])->default('video')->after('title');
            $table->longText('content_body')->nullable()->after('description');
            $table->string('audio_url')->nullable()->after('video_url');
            $table->boolean('is_free')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('learning_lessons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('learning_chapter_id');
            $table->dropColumn(['type', 'content_body', 'audio_url', 'is_free']);
        });

        Schema::dropIfExists('learning_chapters');
    }
};
