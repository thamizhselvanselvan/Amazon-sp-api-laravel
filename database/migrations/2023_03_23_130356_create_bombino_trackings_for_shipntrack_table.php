<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBombinoTrackingsForShipntrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('bombino_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('awb_no', 255)->index('awbno_index');
            $table->string('consignee', 255)->nullable();
            $table->string('consignor', 255)->nullable();
            $table->string('destination', 10)->nullable();
            $table->string('forwarding_no', 255)->nullable();
            $table->string('hawb_no', 255)->nullable();
            $table->string('origin', 10)->nullable();
            $table->string('ship_date')->nullable()->default('0000-00-00 00:00:00');
            $table->string('status', 100)->nullable();
            $table->string('weight', 255)->nullable();
            $table->string('action_date')->nullable()->default('0000-00-00');
            $table->string('action_time')->nullable()->default('00:00:00');
            $table->string('event_code', 255)->nullable();
            $table->string('event_detail', 255)->nullable();
            $table->string('exception', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->unique(['awb_no', 'action_date', 'event_detail'], 'awbno_actionDate_eventDetail_unique');
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
        Schema::connection('shipntracking')->dropIfExists('bombino_trackings');
    }
}
