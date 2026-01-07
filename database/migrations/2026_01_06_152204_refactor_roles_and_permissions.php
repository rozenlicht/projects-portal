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

        // Rename "Supervisor" role to "Staff member - supervisor"
        $supervisorRole = Role::where('name', 'Supervisor')->first();
        if ($supervisorRole) {
            $supervisorRole->update(['name' => 'Staff member - supervisor']);
        }

        // Create "Researcher" role
        $researcher = Role::firstOrCreate(['name' => 'Researcher']);

        // Ensure all necessary permissions exist
        $permissions = [
            'view projects',
            'create projects',
            'update projects',
            'delete projects',
            'manage tags',
            'manage users',
            'manage organizations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to Researcher
        $researcher->givePermissionTo([
            'view projects',
            'create projects',
            'update projects',
            'manage organizations',
        ]);

        // Ensure Staff member - supervisor has the right permissions
        $staffSupervisor = Role::where('name', 'Staff member - supervisor')->first();
        if ($staffSupervisor) {
            $staffSupervisor->syncPermissions([
                'view projects',
                'create projects',
                'update projects',
                'manage organizations',
            ]);
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Rename "Staff member - supervisor" back to "Supervisor"
        $staffSupervisorRole = Role::where('name', 'Staff member - supervisor')->first();
        if ($staffSupervisorRole) {
            $staffSupervisorRole->update(['name' => 'Supervisor']);
        }

        // Delete "Researcher" role
        $researcher = Role::where('name', 'Researcher')->first();
        if ($researcher) {
            $researcher->delete();
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
