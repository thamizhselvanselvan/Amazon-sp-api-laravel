<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBombinoTrackingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('bombino_tracking_details', function (Blueprint $table) {
            $table->id();
            $table->string('awbno');
            $table->dateTime('action_date')->nullable();
            $table->dateTime('action_time')->nullable();
            $table->string('event_code')->nullable();
            $table->string('event_details')->nullable();
            $table->string('exception')->nullable();
            $table->string('location')->nullable();
            $table->unique(["awbno", "action_date", "exception"], 'awbno_action_date_exeption_unique');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('bombino_tracking_details');
    }
}
