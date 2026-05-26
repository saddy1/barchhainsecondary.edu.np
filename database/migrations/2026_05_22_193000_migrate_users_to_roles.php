<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrate existing users to use Spatie roles
     * Maps old is_admin/is_super_admin flags to new roles
     */
    public function up(): void
    {
        // Get all users and assign default roles
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            // Assign teacher role by default (can be manually changed later)
            // In production, you'd want to map based on actual user type
            DB::table('model_has_roles')
                ->updateOrInsert(
                    [
                        'role_id' => $this->getRoleId('teacher'),
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ],
                    [
                        'role_id' => $this->getRoleId('teacher'),
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]
                );
        }
    }

    private function getRoleId(string $roleName): int
    {
        $role = DB::table('roles')->where('name', $roleName)->first();
        return $role ? $role->id : 1; // Default to first role if not found
    }

    private function roleExists(string $roleName): bool
    {
        return DB::table('roles')->where('name', $roleName)->exists();
    }

    private function mapOldRole(string $oldRole): string
    {
        $mapping = [
            'admin' => 'administrator',
            'super_admin' => 'super-admin',
            'teacher' => 'teacher',
            'staff' => 'staff',
            'student' => 'student',
            'principal' => 'principal',
            'accountant' => 'accountant',
        ];

        return $mapping[strtolower($oldRole)] ?? 'teacher';
    }

    public function down(): void
    {
        // Clear all user roles (keep permission tables intact)
        DB::table('model_has_roles')->where('model_type', 'App\\Models\\User')->delete();
    }
};
