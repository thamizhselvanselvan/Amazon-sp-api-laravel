<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Seller_Management extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolesllerManager = Role::create(['name' => 'Seller']);

        $permissionsllerAccount = Permission::create(['name' => 'Seller']);

        $rolesllerManager->givePermissionTo($permissionsllerAccount);
    }
}
