<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NewsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Add new permissions
        Permission::firstOrCreate(['name' => 'manage_news']);
        Permission::firstOrCreate(['name' => 'view_news']);
        Permission::firstOrCreate(['name' => 'create_news']);
        Permission::firstOrCreate(['name' => 'edit_news']);
        Permission::firstOrCreate(['name' => 'delete_news']);

        // Assign them to admin role if exists
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'manage_news',
                'view_news',
                'create_news',
                'edit_news',
                'delete_news',
            ]);
        }
    }
}
