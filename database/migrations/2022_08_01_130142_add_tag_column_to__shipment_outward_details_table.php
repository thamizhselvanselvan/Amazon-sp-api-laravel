<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagColumnToShipmentOutwardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('shipments_outward_details', function (Blueprint $table) {
            $table->string('tag')->after('item_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shipments_outward_details', function (Blueprint $table) {
            $table->dropColumn('tag');
        });
    }
}
