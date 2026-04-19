<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view_profit',
            'manage_marketing',
            'manage_orders',
            'manage_team',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create Roles and assign permissions
        Role::findOrCreate('Manager', 'web')
            ->syncPermissions(Permission::all());

        Role::findOrCreate('Support', 'web')
            ->syncPermissions(['manage_orders']);

        Role::findOrCreate('Marketer', 'web')
            ->syncPermissions(['manage_marketing']);
    }
}
