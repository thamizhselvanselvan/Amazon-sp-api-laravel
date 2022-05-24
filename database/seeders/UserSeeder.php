<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleUser = Role::create(['name' => 'User']);
        $roleAmazon = Role::create(['name' => 'Amazon']);
        $roleB2cship = Role::create(['name' => 'B2CShip']);
        $roleInvManager = Role::create(['name' => 'Inventory']);

        $permissionAdmin = Permission::create(['name' => 'Admin']);
        $permissionUser = Permission::create(['name' => 'User']);
        $permissionAmazon = Permission::create(['name' => 'Amazon']);
        $permissionB2schip = Permission::create(['name' => 'B2CShip']);
        $permissionInvAccount = Permission::create(['name' => 'Inventory']);
       
        $roleAdmin->givePermissionTo($permissionAdmin);
        $roleUser->givePermissionTo($permissionUser);
        $roleAmazon->givePermissionTo($permissionAmazon);
        $roleB2cship->givePermissionTo($permissionB2schip);
        $roleInvManager->givePermissionTo($permissionInvAccount);

        $robin = User::create([
            'name' => 'Robin Singh',
            'email' => 'contact@palmatesolutions.com',
            'password' => Hash::make(123456),
        ]);


        $amit = User::create([
            'name' => 'Amit Kumar',
            'email' => 'nu.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);

        $satish = User::create([
            'name' => 'Kappa',
            'email' => 'kappa.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);

        $sanjay = User::create([
            'name' => 'Sanjay K',
            'email' => 'epsilon.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);

        $am = User::create([
            'name' => 'Amit Mishra',
            'email' => 'am@moshecom.com',
            'password' => Hash::make(123456),
        ]);

        $vikesh = User::create([
            'name'  => 'Vikesh kumar',
            'email' =>  'zeta.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);

        $robin->assignRole('Admin');
        $amit->assignRole('Admin');
        $satish->assignRole('Admin');
        $sanjay->assignRole('Admin');
        $am->assignRole('Admin');
        $vikesh->assignRole('Admin');
        
    }
}
