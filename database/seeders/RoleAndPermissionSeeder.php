<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions (add more as needed)
        $permissions = [
            'view dashboard',
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'adjust stock',
            'view stock history',
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view reports',
            'export reports',
            'manage company settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $owner      = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $staff      = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        // Assign all permissions to super_admin and owner
        $superAdmin->givePermissionTo(Permission::all());
        $owner->givePermissionTo(Permission::all());

        // Admin gets most permissions but not company settings
        $admin->givePermissionTo([
            'view dashboard',
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'adjust stock',
            'view stock history',
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view reports',
            'export reports',
        ]);

        // Staff gets read‑only access
        $staff->givePermissionTo([
            'view dashboard',
            'view clients',
            'view products',
            'view stock history',
            'view invoices',
            'view reports',
        ]);
    }
}
