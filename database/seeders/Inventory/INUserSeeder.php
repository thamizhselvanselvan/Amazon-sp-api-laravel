<?php

namespace Database\Seeders\Inventory;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\InventoryUserSeeder;

class INUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleInvManager = Role::create(['name' => 'Inventory']);

        $permissionInvAccount = Permission::create(['name' => 'Inventory']);

        $roleInvManager->givePermissionTo($permissionInvAccount);
    }
}
