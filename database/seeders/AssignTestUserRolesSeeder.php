<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignTestUserRolesSeeder extends Seeder
{
    /**
     * Assign test roles to existing users or create test users
     * Usage: php artisan db:seed --class=AssignTestUserRolesSeeder
     */
    public function run(): void
    {
        // Find or create test users and assign roles
        // Note: In production, user roles should be assigned via admin panel

        // Find first user and assign super-admin role (usually the original admin)
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('super-admin')) {
            $firstUser->assignRole('super-admin');
            $this->command->info("✓ Assigned 'super-admin' role to user: {$firstUser->name}");
        }

        // Optionally create test users for each role
        $testRoles = [
            'principal' => 'Principal Test User',
            'accountant' => 'Accountant Test User',
            'administrator' => 'Administrator Test User',
            'teacher' => 'Teacher Test User',
            'staff' => 'Staff Test User',
            'student' => 'Student Test User',
        ];

        foreach ($testRoles as $role => $name) {
            $user = User::where('email', "test.{$role}@school.local")->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => "test.{$role}@school.local",
                    'password' => bcrypt('password'),
                    'is_active' => true,
                ]);
                $this->command->info("✓ Created test user: {$user->name} ({$user->email})");
            }

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
                $this->command->info("✓ Assigned '{$role}' role to user: {$user->name}");
            }
        }

        $this->command->info("\n📋 Test users created. Credentials: password='password'");
    }
}
