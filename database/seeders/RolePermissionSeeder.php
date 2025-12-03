<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $administrator = Role::firstOrCreate(['name' => 'Administrator']);
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor']);

        // Create permissions
        $permissions = [
            'view projects',
            'create projects',
            'update projects',
            'delete projects',
            'manage tags',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Administrator
        $administrator->givePermissionTo(Permission::all());

        // Assign limited permissions to Supervisor
        $supervisor->givePermissionTo([
            'view projects',
            'create projects',
            'update projects',
        ]);
    }
}
