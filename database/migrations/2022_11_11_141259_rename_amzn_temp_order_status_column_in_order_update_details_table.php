<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAmznTempOrderStatusColumnInOrderUpdateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_update_details', function (Blueprint $table) {
            $table->renameColumn('amzn_temp_order_status', 'order_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('order_update_details', function (Blueprint $table) {
            //
            $table->renameColumn('order_status', 'amzn_temp_order_status');
        });
    }
}
