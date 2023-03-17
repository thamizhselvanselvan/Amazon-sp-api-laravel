<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPacketForwardersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('packet_forwarders');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->create('packet_forwarders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 20);
            $table->string('awb_no', 20)->nullable();
            $table->string('forwarder_1', 20)->nullable();
            $table->string('forwarder_1_awb', 20)->nullable();
            $table->string('forwarder_2', 20)->nullable();
            $table->string('forwarder_2_awb', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->unique(['order_id', 'awb_no'], 'order_id_awb_no_unique');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
