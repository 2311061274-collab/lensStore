<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'manage_goods_receipts']);
        Permission::firstOrCreate(['name' => 'manage_goods_issues']);
        Permission::firstOrCreate(['name' => 'manage_qc_inspections']);

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'manage_goods_receipts',
                'manage_goods_issues',
                'manage_qc_inspections'
            ]);
        }
    }
}
