<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStatusMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('courier_status_masters');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->create('courier_status_masters', function (Blueprint $table) {
            $table->id();
            $table->string('courier_partner_id')->nullable();
            $table->string('courier_partner_status')->nullable();
            $table->string('booking_master_id')->nullable();
            $table->tinyInteger('stop_tracking')->default('0')->nullable()->comment('0-continue Tracking, 1-stop Trackng');
            $table->timestamps();

            $table->unique(['courier_partner_status', 'courier_partner_id'], 'cp_status_cp_id_unique');
        });
    }
}
