<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes on role and permission tables
     * Ensures quick response times for permission checks
     */
    public function up(): void
    {
        // Add indexes to model_has_roles for quick lookups
        Schema::table('model_has_roles', function (Blueprint $table) {
            // Check and create indexes if they don't exist
            if (!$this->indexExists('model_has_roles', 'model_has_roles_model_id_model_type_index')) {
                $table->index(['model_id', 'model_type']);
            }
        });

        // Add indexes to model_has_permissions for quick lookups
        Schema::table('model_has_permissions', function (Blueprint $table) {
            if (!$this->indexExists('model_has_permissions', 'model_has_permissions_model_id_model_type_index')) {
                $table->index(['model_id', 'model_type']);
            }
        });

        // Add indexes to role_has_permissions for quick lookups
        Schema::table('role_has_permissions', function (Blueprint $table) {
            if (!$this->indexExists('role_has_permissions', 'role_has_permissions_role_id_index')) {
                $table->index('role_id');
            }
        });

        // Add indexes to users table for quick role lookups
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_email_index')) {
                $table->index('email');
            }
            if (!Schema::hasIndex('users', 'users_organization_id_index')) {
                $table->index('organization_id');
            }
            if (!Schema::hasIndex('users', 'users_is_active_index')) {
                $table->index('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_id_model_type_index');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_model_id_model_type_index');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropIndex('role_has_permissions_role_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_index');
            $table->dropIndex('users_organization_id_index');
            $table->dropIndex('users_is_active_index');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        // Simple check if index exists
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SHOW INDEX FROM {$table} WHERE Key_name = ?",
                [$indexName]
            );
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
