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

        $permissionAdmin = Permission::create(['name' => 'Admin']);
        $permissionUser = Permission::create(['name' => 'User']);

        $roleAdmin->givePermissionTo($permissionAdmin);
        $roleUser->givePermissionTo($permissionUser);

        $robin = User::create([
            'name' => 'Robin Singh',
            'email' => 'robin.flyhigh@gmail.com',
            'password' => Hash::make(123456),
            
        ]);

        $amitkumar = User::create([
            'name' => 'Amit Kumar',
            'email' => 'nu.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);

        $satish = User::create([
            'name' => 'Kappa',
            'email' => 'kappa.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);

        $gopal = User::create([
            'name' => 'Gopal',
            'email' => 'phi.palmate@gmail.com',
            'password' => Hash::make(123456),
        ]);


        $robin->assignRole('Admin');
        $amitkumar->assignRole('Admin');
        $satish->assignRole('User');
        $gopal->assignRole('User');
    }
}
