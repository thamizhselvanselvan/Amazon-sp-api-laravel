<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreateStopPacketTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('stop_packet_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('forwarder', 100)->nullable();
            $table->string('tracking_status', 255)->nullable();
            $table->unique(['forwarder', 'tracking_status'], 'forwarder_status_unique');
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
        Schema::connection('shipntracking')->dropIfExists('stop_packet_trackings');
    }
}
