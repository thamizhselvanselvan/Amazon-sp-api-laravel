<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class Catalog_Manager extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleCatalog = Role::create(['name' => 'Catalog']);
        $permissionCatalog = Permission::create(['name' => 'Catalog']);

        $roleCatalog->givePermissionTo($permissionCatalog);
    }
}
