<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->string('label', 100);
            $table->string('description', 255)->nullable();
            $table->string('group', 60)->default('General');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        DB::table('module_settings')->insert([
            ['key' => 'vacancy',      'label' => 'Vacancies',           'description' => 'Job postings and online applications',                'group' => 'Website',  'is_enabled' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admissions',   'label' => 'Admissions',          'description' => 'Online admission enquiry form and management',        'group' => 'Website',  'is_enabled' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'card',         'label' => 'ID Card System',      'description' => 'Student, staff and teacher ID card management',       'group' => 'ERP',      'is_enabled' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hajiri',       'label' => 'Hajiri (Attendance)', 'description' => 'Full attendance, payroll and HR ERP module',          'group' => 'ERP',      'is_enabled' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hajiri_leave', 'label' => 'Leave Management',    'description' => 'Leave requests, policies and approvals within Hajiri','group' => 'Hajiri',   'is_enabled' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('module_settings');
    }
};
