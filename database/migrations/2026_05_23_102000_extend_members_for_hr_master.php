<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender', 30)->nullable()->after('dob');
            $table->string('blood_group', 10)->nullable()->after('gender');
            $table->string('father_name', 150)->nullable()->after('guardian_name');
            $table->string('mother_name', 150)->nullable()->after('father_name');
            $table->string('grandfather_name', 150)->nullable()->after('mother_name');
            $table->string('parent_contact', 30)->nullable()->after('mobile');
            $table->string('emergency_contact_name', 150)->nullable()->after('parent_contact');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');

            $table->string('permanent_province', 100)->nullable()->after('country');
            $table->string('permanent_district', 100)->nullable()->after('permanent_province');
            $table->string('permanent_municipality', 150)->nullable()->after('permanent_district');
            $table->string('permanent_ward', 20)->nullable()->after('permanent_municipality');
            $table->string('permanent_tole', 150)->nullable()->after('permanent_ward');
            $table->string('temporary_province', 100)->nullable()->after('permanent_tole');
            $table->string('temporary_district', 100)->nullable()->after('temporary_province');
            $table->string('temporary_municipality', 150)->nullable()->after('temporary_district');
            $table->string('temporary_ward', 20)->nullable()->after('temporary_municipality');
            $table->string('temporary_tole', 150)->nullable()->after('temporary_ward');

            $table->string('employee_category', 40)->nullable()->after('employment_type');
            $table->date('joining_date')->nullable()->after('employee_category');
            $table->date('permanent_date')->nullable()->after('joining_date');
            $table->string('bank_name', 150)->nullable()->after('permanent_date');
            $table->string('bank_branch', 150)->nullable()->after('bank_name');
            $table->string('bank_account_name', 150)->nullable()->after('bank_branch');
            $table->string('bank_account_number', 80)->nullable()->after('bank_account_name');
            $table->string('pan_number', 80)->nullable()->after('bank_account_number');
            $table->string('ssf_number', 80)->nullable()->after('pan_number');
            $table->string('cit_number', 80)->nullable()->after('ssf_number');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'blood_group',
                'father_name',
                'mother_name',
                'grandfather_name',
                'parent_contact',
                'emergency_contact_name',
                'emergency_contact_phone',
                'permanent_province',
                'permanent_district',
                'permanent_municipality',
                'permanent_ward',
                'permanent_tole',
                'temporary_province',
                'temporary_district',
                'temporary_municipality',
                'temporary_ward',
                'temporary_tole',
                'employee_category',
                'joining_date',
                'permanent_date',
                'bank_name',
                'bank_branch',
                'bank_account_name',
                'bank_account_number',
                'pan_number',
                'ssf_number',
                'cit_number',
            ]);
        });
    }
};
