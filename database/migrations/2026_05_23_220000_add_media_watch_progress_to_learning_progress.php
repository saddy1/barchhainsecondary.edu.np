<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_progress', function (Blueprint $table) {
            $table->unsignedInteger('current_seconds')->default(0)->after('progress_percent');
            $table->unsignedInteger('max_watched_seconds')->default(0)->after('current_seconds');
            $table->unsignedInteger('media_duration_seconds')->default(0)->after('max_watched_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('learning_progress', function (Blueprint $table) {
            $table->dropColumn(['current_seconds', 'max_watched_seconds', 'media_duration_seconds']);
        });
    }
};
