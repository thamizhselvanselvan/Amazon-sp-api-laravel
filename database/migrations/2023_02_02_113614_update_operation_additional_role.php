<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateOperationAdditionalRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        $arrays = ['ITTeam', 'Management AM & MM'];
        foreach ($arrays as $array) {
            $role = Role::findByName($array);
            $permission = Permission::findByName($array);
            $role->revokePermissionTo($permission);
            $role->delete();
            $permission->delete();
        }
        $arrays = ['IT', 'Management'];
        foreach ($arrays as $array) {
            $role = Role::create(['name' => $array]);
            $permission = Permission::create(['name' => $array]);
            $role ->givePermissionTo($permission);
        }
        

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $arrays = ['ITTeam', 'Management AM & MM'];
        foreach ($arrays as $array) {
            $role = Role::create(['name' => $array]);
            $permission = Permission::create(['name' => $array]);
            $role ->givePermissionTo($permission);
        }
        $arrays = ['IT', 'Management'];
        foreach ($arrays as $array) {
            $role = Role::findByName($array);
            $permission = Permission::findByName($array);
            $role->revokePermissionTo($permission);
            $role->delete();
            $permission->delete();
        }
    }
}
