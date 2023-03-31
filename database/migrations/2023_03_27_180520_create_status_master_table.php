<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('status_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courier_id')->nullable();
           $table->foreign('courier_id')->references('id')->on('courier');
            $table->string('courier_status')->nullable();
            $table->unsignedBigInteger('booking_master_id')->nullable();
            $table->foreign('booking_master_id')->references('id')->on('booking_masters');
            $table->tinyInteger('stop_tracking')->default('0')->nullable()->comment('0-continue Tracking, 1-stop Trackng');
            $table->timestamps();

            $table->unique(['courier_status', 'courier_id'], 'cp_status_cp_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('status_master');
    }
}
