<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Boe_Account extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleBoeAccounts = Role::create(['name' => 'BOE']);
        $permissionBoeAccounts = Permission::create(['name' => 'BOE']);

        $roleBoeAccounts->givePermissionTo($permissionBoeAccounts);
    }
}
