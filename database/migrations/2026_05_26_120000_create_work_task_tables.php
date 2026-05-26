<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('work_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_group_id')->constrained('work_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['work_group_id', 'user_id']);
        });

        Schema::create('work_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->date('due_date');
            $table->unsignedInteger('max_score')->default(10);
            $table->decimal('incentive_amount', 12, 2)->nullable();
            $table->enum('assignment_type', ['individual', 'group'])->default('individual');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('work_group_id')->nullable()->constrained('work_groups')->nullOnDelete();
            $table->enum('group_submission_mode', ['individual', 'shared'])->nullable();
            $table->enum('group_payment_mode', ['equal', 'score'])->nullable();
            $table->decimal('late_penalty_percent', 5, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['assignment_type', 'due_date']);
        });

        Schema::create('work_task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_task_id')->constrained('work_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('work_group_id')->nullable()->constrained('work_groups')->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comment')->nullable();
            $table->string('evidence_link')->nullable();
            $table->string('evidence_file')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['submitted', 'approved', 'rejected'])->default('submitted');
            $table->unsignedInteger('score')->nullable();
            $table->text('review_note')->nullable();
            $table->decimal('payout_amount', 12, 2)->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['work_task_id', 'status']);
        });

        if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
            $permissions = [
                'work-tasks.view',
                'work-tasks.create',
                'work-tasks.submit',
                'work-tasks.review',
                'work-groups.manage',
            ];

            foreach ($permissions as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $permission, 'guard_name' => 'web'],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            $rolePermissions = [
                'super-admin' => $permissions,
                'principal' => $permissions,
                'administrator' => $permissions,
                'teacher' => ['work-tasks.view', 'work-tasks.submit'],
            ];

            foreach ($rolePermissions as $role => $rolePermissionNames) {
                $roleId = DB::table('roles')->where('name', $role)->where('guard_name', 'web')->value('id');
                if (! $roleId) {
                    continue;
                }

                $permissionIds = DB::table('permissions')->whereIn('name', $rolePermissionNames)->pluck('id');
                foreach ($permissionIds as $permissionId) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_task_submissions');
        Schema::dropIfExists('work_tasks');
        Schema::dropIfExists('work_group_user');
        Schema::dropIfExists('work_groups');
    }
};
