<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnstoShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('in')->table('shipments', function (Blueprint $table) {
            $table->renameColumn('Ship_id', 'ship_id');
            $table->string('asin')->after('ship_id');
            $table->string('item_name')->after('asin');
            $table->integer('price')->after('item_name');
            $table->integer('quantity')->after('price');
            $table->dropColumn('old_quantity');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('in')->table('shipments', function (Blueprint $table) {
            $table->dropColumn(['asin','item_name','price','quantity']);
            $table->renameColumn('ship_id','Ship_id');
            $table->integer('old_quantity');
    
        });
    }
}
