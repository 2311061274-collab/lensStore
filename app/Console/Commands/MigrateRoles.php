<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Signature('roles:migrate')]
#[Description('Migrate legacy roles to Spatie permissions')]
class MigrateRoles extends Command
{
    public function handle()
    {
        // Clear cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define base permissions
        $permissions = [
            'view_dashboard',
            'manage_orders',
            'manage_products',
            'manage_categories',
            'manage_customers',
            'manage_vouchers',
            'manage_users',
            'view_reports',
            'manage_roles',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create base roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions(['view_dashboard', 'manage_orders', 'manage_products', 'manage_categories']);

        Role::firstOrCreate(['name' => 'customer']);

        $this->info('Spatie Roles and permissions created successfully.');

        // Migrate existing users
        $users = User::all();
        $count = 0;
        foreach ($users as $user) {
            // Assign role based on the old 'role' string column
            if ($user->role && Role::where('name', $user->role)->exists()) {
                if (! $user->hasRole($user->role)) {
                    $user->assignRole($user->role);
                    $count++;
                }
            }
        }

        $this->info("Migrated {$count} users to Spatie roles.");
    }
}
