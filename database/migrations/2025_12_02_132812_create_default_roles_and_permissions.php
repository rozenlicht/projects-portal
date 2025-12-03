<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions from roles
        $administrator = Role::findByName('Administrator');
        $supervisor = Role::findByName('Supervisor');

        if ($administrator) {
            $administrator->revokePermissionTo(Permission::all());
        }

        if ($supervisor) {
            $supervisor->revokePermissionTo(Permission::all());
        }

        // Delete permissions
        Permission::whereIn('name', [
            'view projects',
            'create projects',
            'update projects',
            'delete projects',
            'manage tags',
            'manage users',
        ])->delete();

        // Delete roles
        Role::whereIn('name', ['Administrator', 'Supervisor'])->delete();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
