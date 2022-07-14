<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsaTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smsa_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('awbno');
            $table->dateTime('date');
            $table->string('activity');
            $table->string('details');
            $table->string('location');
            $table->unique(["awbno", "date", "activity"], 'awbno_date_activity_unique');
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
        Schema::dropIfExists('smsa_trackings');
    }
}
