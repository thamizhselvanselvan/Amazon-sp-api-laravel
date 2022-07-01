<?php


use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $invoice = Role::create(['name' => 'Invoice']);
        $label = Role::create(['name' => 'Label']);
        $order = Role::create(['name' => 'Orders']);

        $invoice_permission = Permission::create(['name' => 'Invoice']);
        $label_permission = Permission::create(['name' => 'Label']);
        $order_permission = Permission::create(['name' => 'Orders']);

        $invoice->givePermissionTo($invoice_permission);
        $label->givePermissionTo($label_permission);
        $order->givePermissionTo($order_permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
