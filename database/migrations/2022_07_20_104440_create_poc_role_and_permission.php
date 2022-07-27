<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePocRoleAndPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $POC = Role::create(['name' => 'POC']);

        $POC_permission = Permission::create(['name' => 'POC']);

        $POC->givePermissionTo($POC_permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $POC = Role::findByName('POC');
        $POC_permission = Permission::findByName('POC');

        $POC->revokePermissionTo($POC_permission);

        $POC->delete();
        $POC_permission->delete();
    }
}
