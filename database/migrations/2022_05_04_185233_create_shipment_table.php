<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id');
            $table->string('Ship_id');
            $table->text('asin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->dropIfExists('shipments');
    }
}
