<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdColumnInRacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('racks', function (Blueprint $table) {
            $table->integer("rack_id");
            $table->integer("warehouse_id");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('racks', function (Blueprint $table) {
            $table->dropColumn('rack_id');
            $table->dropColumn('warehouse_id');
        });
    }
}
