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
            Schema::connection('inventory')->dropIfExists('shipments');
        }

    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('source_id');
            $table->string('Ship_id');
            $table->string('quantity');
            $table->timestamps();
    
        });
    }

}