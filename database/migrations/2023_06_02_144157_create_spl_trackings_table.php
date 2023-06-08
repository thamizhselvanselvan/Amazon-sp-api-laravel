<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSplTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('spl_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('airwaybill', 20);
            $table->string('eventCode')->nullable();
            $table->string('event')->nullable();
            $table->string('eventName')->nullable();
            $table->string('supplier')->nullable();
            $table->string('userName')->nullable();
            $table->string('notes')->nullable();
            $table->dateTime('actionDate')->nullable();
            $table->string('eventCountry')->nullable();
            $table->string('eventCity')->nullable();
            $table->string('eventSubCode')->nullable();
            $table->string('eventSubName')->nullable();
            $table->timestamps();


            $table->index('airwaybill', 'airwaybill_index');
            $table->unique(['airwaybill', 'actionDate', 'event'], 'airwaybill_actionDate_event_unique');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spl_tracking');
    }
}
