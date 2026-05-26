<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->date('dob');
            $table->string('gender');
            $table->string('guardian_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('applied_grade'); // e.g., Nursery, Grade 1, +2 Science
            $table->string('previous_school')->nullable();
            $table->string('status')->default('Pending'); // Pending, Reviewed, Accepted, Rejected
            $table->text('admin_remarks')->nullable(); // Private notes for the school admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};