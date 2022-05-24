<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutshipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->create('outshipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse');
            $table->foreignId('destination_id');
            $table->string('ship_id');
            $table->string('currency');
            $table->text('items');
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
        Schema::connection('inventory')->dropIfExists('outshipments');
    }
}
