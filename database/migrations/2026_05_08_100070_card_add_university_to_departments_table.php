<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Line 1 of the card header  e.g. "TRIBHUVAN UNIVERSITY"
            $table->string('university')->nullable()->after('name');
            // Line 2 of the card header  e.g. "Barchhain Secondary School"
            $table->string('university_college')->nullable()->after('university');
            // Logo image path relative to public/  e.g. "assets/image/logo.png"
            $table->string('university_logo')->nullable()->after('university_college');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['university', 'university_college', 'university_logo']);
        });
    }
};
