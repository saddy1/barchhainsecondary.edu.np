<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->enum('member_type', ['student', 'teacher', 'staff']);
            $table->string('roll_number')->unique();    // e.g. PUR071BEL019 / ST-01 / EL-01
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('dob')->nullable() ;
            $table->string('citizenship_no')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();         // stored path
            $table->string('designation')->nullable();   // for staff/teacher
            $table->string('employment_type')->nullable();
            $table->date('valid_till')->nullable();
            $table->string('program')->nullable();       // e.g. BE Computer
            $table->string('batch')->nullable();         // e.g. 2078
            // Address
            $table->string('zone')->nullable();
            $table->string('district')->nullable();
            $table->string('municipality')->nullable();
            $table->string('country')->default('Nepal');
            // Bus pass
            $table->string('bus_route')->nullable();
            $table->string('bus_stop')->nullable();
            $table->boolean('has_bus_pass')->default(false);
            // Library card
            $table->string('library_id')->nullable();
            $table->boolean('has_library_card')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
