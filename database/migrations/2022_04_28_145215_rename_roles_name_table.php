<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameRolesNameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $role1 = Role::create(['name' => 'Amazon']);
        $role2 = Role::create(['name' => 'B2CShip']);

        Permission::create(['name' => 'Amazon']);
        Permission::create(['name' => 'B2CShip']);

        $role1->givePermissionTo('Amazon');
        $role2->givePermissionTo('B2CShip');

        Role::where('name', 'Catalog Manager')->update([
            'name' => 'Catalog'
        ]);
        Role::where('name','Account')->update([
            'name'=>'BOE'
        ]);
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'Catalog')->update([
            'name' => 'Catalog Manager'
        ]);

        Role::where('name', 'BOE')->update([
            'name' => 'Account'
        ]);
    }
}
