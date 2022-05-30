<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseColumntoShelveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 
        public function up()
        {
            Schema::connection('inventory')->table('shelves', function (Blueprint $table) {
                $table->foreignId('warehouse')->after('id');
            });
        }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shelves', function (Blueprint $table) 
        {
            $table->dropColumn('warehouse');

        });
    }
}
