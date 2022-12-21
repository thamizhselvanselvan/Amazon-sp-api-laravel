<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSourceIdFromIntToCharInInventoryInwardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('inventory')->table('shipments_inward', function (Blueprint $table) {
           
            $table->string('source_id')->nullable()->change();
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::connection('inventory')->table('shipments_inward', function (Blueprint $table) {
            $table->foreignId('source_id')->change();
        });
    }
}
