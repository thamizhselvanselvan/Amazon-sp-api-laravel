<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKycRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $kyc = Role::create(['name' => 'KYC']);

        $kyc_permission = Permission::create(['name' => 'KYC']);

        $kyc->givePermissionTo($kyc_permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $kyc = Role::findByName('KYC');
        $kyc_permission = Permission::findByName('KYC');

        $kyc->revokePermissionTo($kyc_permission);
        
        $kyc->delete();
        $kyc_permission->delete();
    }
}
