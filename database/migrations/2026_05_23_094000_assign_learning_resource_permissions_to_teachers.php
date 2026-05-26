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
            'learning.lessons.view',
            'learning.lessons.create',
            'learning.lessons.edit',
            'learning.resources.view',
            'learning.resources.create',
            'learning.resources.edit',
            'learning.quizzes.view',
            'learning.quizzes.create',
            'learning.quizzes.edit',
            'learning.reports.view',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $teacherRoleId = DB::table('roles')->where('name', 'teacher')->where('guard_name', 'web')->value('id');
        if (! $teacherRoleId) {
            return;
        }

        $permissionIds = DB::table('permissions')->whereIn('name', $permissions)->pluck('id');
        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $permissionId,
                'role_id' => $teacherRoleId,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        $teacherRoleId = DB::table('roles')->where('name', 'teacher')->where('guard_name', 'web')->value('id');
        if (! $teacherRoleId) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', [
                'learning.lessons.create',
                'learning.lessons.edit',
                'learning.resources.create',
                'learning.resources.edit',
                'learning.quizzes.create',
                'learning.quizzes.edit',
                'learning.reports.view',
            ])
            ->pluck('id');

        DB::table('role_has_permissions')
            ->where('role_id', $teacherRoleId)
            ->whereIn('permission_id', $permissionIds)
            ->delete();
    }
};
