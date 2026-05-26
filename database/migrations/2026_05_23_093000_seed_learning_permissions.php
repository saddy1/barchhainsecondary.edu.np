<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        $permissions = [
            'learning.courses.view',
            'learning.courses.create',
            'learning.courses.edit',
            'learning.courses.delete',
            'learning.students.view',
            'learning.students.create',
            'learning.students.edit',
            'learning.students.delete',
            'learning.lessons.view',
            'learning.lessons.create',
            'learning.lessons.edit',
            'learning.lessons.delete',
            'learning.resources.view',
            'learning.resources.create',
            'learning.resources.edit',
            'learning.resources.delete',
            'learning.quizzes.view',
            'learning.quizzes.create',
            'learning.quizzes.edit',
            'learning.quizzes.delete',
            'learning.reports.view',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        DB::table('roles')->updateOrInsert(
            ['name' => 'student', 'guard_name' => 'web'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $permissionIds = DB::table('permissions')->whereIn('name', $permissions)->pluck('id', 'name');
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'administrator', 'principal', 'teacher', 'student'])->pluck('id', 'name');

        $rolePermissions = [
            'super-admin' => $permissions,
            'administrator' => $permissions,
            'principal' => [
                'learning.courses.view',
                'learning.courses.create',
                'learning.courses.edit',
                'learning.students.view',
                'learning.students.create',
                'learning.students.edit',
                'learning.lessons.view',
                'learning.lessons.create',
                'learning.lessons.edit',
                'learning.resources.view',
                'learning.quizzes.view',
                'learning.reports.view',
            ],
            'teacher' => [
                'learning.courses.view',
                'learning.lessons.view',
                'learning.resources.view',
                'learning.quizzes.view',
            ],
            'student' => [
                'learning.courses.view',
                'learning.lessons.view',
                'learning.resources.view',
                'learning.quizzes.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $names) {
            $roleId = $roles[$roleName] ?? null;
            if (! $roleId) {
                continue;
            }

            foreach ($names as $name) {
                $permissionId = $permissionIds[$name] ?? null;
                if (! $permissionId) {
                    continue;
                }

                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        DB::table('permissions')->where('name', 'like', 'learning.%')->delete();
    }
};
