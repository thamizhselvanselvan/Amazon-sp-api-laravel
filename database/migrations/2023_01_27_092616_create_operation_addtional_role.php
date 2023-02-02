<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateOperationAddtionalRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('operation_addtional_role', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        $operationPurchase = Role::create(['name' => 'Operations - Purchase']);
        $operationTracking = Role::create(['name' => 'Operations - Tracking']);
        $operationDispatch = Role::create(['name' => 'Operations - Dispatch']);
        $operationInventory = Role::create(['name' => 'Operations - Inventory']);
        $operationManager = Role::create(['name' => 'Operations - Manager']);
        $catalogManager = Role::create(['name' => 'Catalog - Manager']);
        $catalogMember = Role::create(['name' => 'Catalog - Member']);
        $accountManager = Role::create(['name' => 'Account - Manager']);
        $accountMember = Role::create(['name' => 'Account - Member']);
        $itTeam = Role::create(['name' => 'ITTeam']);
        $manageAM = Role::create(['name' => 'Management AM & MM']);
        

        $operationPurchase_permission = Permission::create(['name' => 'Operations - Purchase']);
        $operationTracking_permission = Permission::create(['name' => 'Operations - Tracking']);
        $operationDispatch_permission = Permission::create(['name' => 'Operations - Dispatch']);
        $operationInventory_permission = Permission::create(['name' => 'Operations - Inventory']);
        $operationManager_permission = Permission::create(['name' => 'Operations - Manager']);
        $catalogManager_permission = Permission::create(['name' => 'Catalog - Manager']);
        $catalogMember_permission = Permission::create(['name' => 'Catalog - Member']);
        $accountManager_permission = Permission::create(['name' => 'Account - Manager']);
        $accountMember_permission = Permission::create(['name' => 'Account - Member']);
        $itTeam_permission = Permission::create(['name' => 'ITTeam']);
        $manageAM_permission = Permission::create(['name' => 'Management AM & MM']);

        $operationPurchase->givePermissionTo($operationPurchase_permission);
        $operationTracking->givePermissionTo($operationTracking_permission);
        $operationDispatch->givePermissionTo($operationDispatch_permission);
        $operationInventory->givePermissionTo($operationInventory_permission );
        $operationManager->givePermissionTo($operationManager_permission);
        $catalogManager->givePermissionTo($catalogManager_permission );
        $catalogMember->givePermissionTo($catalogMember_permission );
        $accountManager->givePermissionTo($accountManager_permission);
        $accountMember->givePermissionTo($accountMember_permission);
        $itTeam ->givePermissionTo($itTeam_permission);
        $manageAM->givePermissionTo($manageAM_permission);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
        $operationPurchase = Role::findByName('Operations - Purchase');
        $operationPurchase_permission = Permission::findByName('Operations - Purchase');
        $operationPurchase->revokePermissionTo($operationPurchase_permission);
        $operationPurchase->delete();
        $operationPurchase_permission->delete();

        $operationTracking = Role::findByName('Operations - Tracking');
        $operationTracking_permission = Permission::findByName('Operations - Tracking');
        $operationTracking->revokePermissionTo($operationTracking_permission);
        $operationTracking->delete();
        $operationTracking_permission->delete();

        $operationDispatch = Role::findByName('Operations - Dispatch');
        $operationDispatch_permission = Permission::findByName('Operations - Dispatch');
        $operationDispatch->revokePermissionTo($operationDispatch_permission);
        $operationDispatch->delete();
        $operationDispatch_permission->delete();

        $operationInventory = Role::findByName('Operations - Inventory');
        $operationInventory_permission = Permission::findByName('Operations - Inventory');
        $operationInventory->revokePermissionTo($operationInventory_permission);
        $operationInventory->delete();
        $operationInventory_permission->delete();

        $operationManager = Role::findByName('Operations - Manager');
        $operationManager_permission = Permission::findByName('Operations - Manager');
        $operationManager->revokePermissionTo($operationManager_permission);
        $operationManager->delete();
        $operationManager_permission->delete();

        $catalogManager = Role::findByName('Catalog - Manager');
        $catalogManager_permission = Permission::findByName('Catalog - Manager');
        $catalogManager->revokePermissionTo($catalogManager_permission);
        $catalogManager->delete();
        $catalogManager_permission->delete();

        $catalogMember = Role::findByName('Catalog - Member');
        $catalogMember_permission = Permission::findByName('Catalog - Member');
        $catalogMember->revokePermissionTo($catalogMember_permission);
        $catalogMember->delete();
        $catalogMember_permission->delete();

        $accountManager = Role::findByName('Account - Manager');
        $accountManager_permission = Permission::findByName('Account - Manager');
        $accountManager->revokePermissionTo($accountManager_permission);
        $accountManager->delete();
        $accountManager_permission->delete();

        $accountMember = Role::findByName('Account - Member');
        $accountMember_permission = Permission::findByName('Account - Member');
        $accountMember->revokePermissionTo($accountMember_permission);
        $accountMember->delete();
        $accountMember_permission->delete();

        $itTeam = Role::findByName('ITTeam');
        $itTeam_permission = Permission::findByName('ITTeam');
        $itTeam->revokePermissionTo($itTeam_permission);
        $itTeam->delete();
        $itTeam_permission->delete();

        $manageAM = Role::findByName('Management AM & MM');
        $manageAM_permission = Permission::findByName('Management AM & MM');
        $manageAM->revokePermissionTo($manageAM_permission);
        $manageAM->delete();
        $manageAM_permission->delete();





    }
}
