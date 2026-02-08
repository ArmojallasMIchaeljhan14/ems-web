<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Admin
            'view admin dashboard',
            'manage approvals',
            'manage scheduling',
            'manage venues',
            'manage posts',
            'manage participants',
            'adjust events',
            'view reports',
            'send documents',
            // User
            'view user dashboard',
            'request events',
            'respond documents',
            'view requests status',
            'view calendar',
            'view schedule',
            'view posts',
            'create posts',
            'comment posts',
            'view program flow',
            'contact support',
            // Multimedia staff
            'view media dashboard',
            'manage all posts',
            'view schedule and events',
            'receive support',
        ];

        foreach (array_unique($permissions) as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $mediaRole = Role::firstOrCreate(['name' => 'multimedia_staff', 'guard_name' => 'web']);

        $adminRole->syncPermissions([
            'view admin dashboard', 'manage approvals', 'manage scheduling', 'manage venues',
            'manage posts', 'manage participants', 'adjust events', 'view reports', 'send documents',
        ]);
        $userRole->syncPermissions([
            'view user dashboard', 'request events', 'respond documents', 'view requests status',
            'view calendar', 'view schedule', 'view posts', 'create posts', 'comment posts',
            'view program flow', 'contact support',
        ]);
        $mediaRole->syncPermissions([
            'view media dashboard', 'manage all posts', 'view schedule and events', 'receive support',
        ]);

        $this->assignRolesToExistingUsers();
    }

    private function assignRolesToExistingUsers(): void
    {
        $byEmail = [
            'admin@example.com' => 'admin',
            'user@example.com' => 'user',
            'media@example.com' => 'multimedia_staff',
        ];

        foreach ($byEmail as $email => $roleName) {
            $user = User::where('email', $email)->first();
            if ($user && !$user->hasRole($roleName)) {
                $user->syncRoles([$roleName]);
            }
        }

        foreach (User::all() as $u) {
            if (!$u->hasAnyRole(['admin', 'user', 'multimedia_staff'])) {
                $u->assignRole('user');
            }
        }
    }
}
