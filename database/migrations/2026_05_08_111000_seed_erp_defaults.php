<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('is_super_admin', true)->update([
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 1,
        ]);

        DB::table('users')->where('is_admin', true)->where('is_super_admin', false)->update([
            'role' => 'admin',
            'is_active' => true,
            'status' => 1,
        ]);

        DB::table('organizations')->updateOrInsert(
            ['slug' => 'barchhain-secondary-school'],
            [
                'name' => 'Barchhain Secondary School',
                'type' => 'school',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        foreach (['Administration', 'Academic'] as $label) {
            DB::table('work_assigneds')->updateOrInsert(
                ['label' => $label],
                ['status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach (['Permanent', 'Temporary', 'Contract', 'Service Provider'] as $label) {
            DB::table('employment_types')->updateOrInsert(
                ['label' => $label],
                ['status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach (['Teacher', 'Staff', 'Principal'] as $label) {
            DB::table('designation')->updateOrInsert(
                ['label' => $label],
                ['alias' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        DB::table('hajiri_departments')->updateOrInsert(
            ['label' => 'School'],
            ['alias' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        //
    }
};
