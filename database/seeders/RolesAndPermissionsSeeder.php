<?php

namespace Database\Seeders;

use App\Models\PortalUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_all_requests',
            'view_assigned_requests',
            'assign_technician',
            'update_request_status',
            'manage_portal_users',
            'view_reports',
            'manage_settings',
            'view_payments',
            'send_chat',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'portal']);
        }

        // super_admin — everything
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'portal']);
        $superAdmin->syncPermissions($permissions);

        // admin — all except manage_portal_users
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'portal']);
        $admin->syncPermissions([
            'view_all_requests', 'view_assigned_requests',
            'assign_technician', 'update_request_status',
            'view_reports', 'view_payments', 'send_chat',
        ]);

        // supervisor — view all, update status, send chat
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'portal']);
        $supervisor->syncPermissions([
            'view_all_requests', 'view_assigned_requests',
            'update_request_status', 'view_reports', 'send_chat',
        ]);

        // technician — only assigned requests, chat
        $technician = Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'portal']);
        $technician->syncPermissions([
            'view_assigned_requests', 'update_request_status', 'send_chat',
        ]);

        // Create default super admin user
        $admin = PortalUser::firstOrCreate(
            ['email' => 'admin@gowologlobal.com'],
            [
                'name'      => 'Super',
                'last_name' => 'Admin',
                'password'  => Hash::make('Admin@12345'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');

        $this->command->info('Roles, permissions, and default super admin created.');
        $this->command->info('Login: admin@gowologlobal.com / Admin@12345');
    }
}
