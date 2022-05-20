<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->hasTable('destination', function (Blueprint $table) {
            $table->rename('destination', 'destinations');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->hasTable('destinations', function (Blueprint $table) {
            $table->rename('destinations', 'destination');
        });
    }
}
