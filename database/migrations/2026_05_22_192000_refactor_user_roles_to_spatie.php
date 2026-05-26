<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refactor user roles from boolean columns to Spatie HasRoles
     * Removes deprecated is_admin, is_super_admin columns
     * Normalizes the role column
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop deprecated boolean flags
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('users', 'is_super_admin')) {
                $table->dropColumn('is_super_admin');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            // Add audit fields
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }

            // Add indexes for performance
            if (!Schema::hasColumn('users', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->constrained();
            } else {
                // Ensure foreign key exists
                try {
                    $table->foreign('organization_id')->references('id')->on('organizations')->change();
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });

        // Add indexes for quick response times
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('organization_id');
            $table->index('is_active');
            $table->index('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['email']);
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['last_login_at']);

            // Drop new audit fields
            $table->dropColumn(['last_login_at']);

            // Restore old structure (rollback only)
            $table->boolean('is_admin')->default(false)->after('email');
            $table->boolean('is_super_admin')->default(false)->after('is_admin');
            $table->string('role')->default('employee')->after('is_super_admin');
        });
    }
};
