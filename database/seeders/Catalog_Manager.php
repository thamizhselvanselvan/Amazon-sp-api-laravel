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
        $roleCatalogManager = Role::create(['name' => 'Catalog Manager']);

        $permissionCatalogManager = Permission::create(['name' => 'Catalog Manager']);

        $roleCatalogManager->givePermissionTo($permissionCatalogManager);

        $mudassir = User::create([
            'name' => 'Mudassir',
            'email' => 'mudassir@moshecom.com',
            'password' => Hash::make(123456),
        ]);

        $mudassir->assignRole('Catalog Manager');
    }
}
