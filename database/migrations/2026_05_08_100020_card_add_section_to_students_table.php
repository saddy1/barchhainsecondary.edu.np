<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Section within a stream/class, e.g. "A", "B", "Rose"
            $table->string('section')->nullable()->after('stream');

            // Roll number is no longer globally unique — it is unique per
            // (organization, stream, section) group (handled at app level).
            $table->dropUnique('students_roll_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('section');
            $table->unique('roll_number');
        });
    }
};
