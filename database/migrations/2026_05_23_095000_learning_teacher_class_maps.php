<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teacher-class assignment map
        Schema::create('learning_teacher_class_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('learning_class_id')->constrained('learning_classes')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'learning_class_id']);
        });

        // Track which user created each resource
        if (Schema::hasTable('learning_resources') && !Schema::hasColumn('learning_resources', 'created_by')) {
            Schema::table('learning_resources', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }

        // Add learning.teacher.assign permission and assign to admins
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            return;
        }

        DB::table('permissions')->updateOrInsert(
            ['name' => 'learning.teacher.assign', 'guard_name' => 'web'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $permId = DB::table('permissions')->where('name', 'learning.teacher.assign')->value('id');
        $roles  = ['super-admin', 'administrator', 'principal'];

        foreach ($roles as $roleName) {
            $roleId = DB::table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if ($roleId && $permId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $permId,
                    'role_id'       => $roleId,
                ]);
            }
        }

        // Also grant teachers learning.resources.delete (create/edit already granted)
        DB::table('permissions')->updateOrInsert(
            ['name' => 'learning.resources.delete', 'guard_name' => 'web'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $deletePermId  = DB::table('permissions')->where('name', 'learning.resources.delete')->value('id');
        $teacherRoleId = DB::table('roles')->where('name', 'teacher')->where('guard_name', 'web')->value('id');

        if ($deletePermId && $teacherRoleId) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $deletePermId,
                'role_id'       => $teacherRoleId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_teacher_class_maps');

        if (Schema::hasColumn('learning_resources', 'created_by')) {
            Schema::table('learning_resources', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }
    }
};
