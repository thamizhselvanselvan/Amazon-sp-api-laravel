<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStopAndShowEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('stop_packet_trackings');

        Schema::connection('shipntracking')->create('stop_tracking_show_events', function (Blueprint $table) {
            $table->id();
            $table->string('forwarder_code', 50);
            $table->string('event', 255)->nullable();
            $table->string('show_tracking', 5)->nullable();
            $table->string('stop_tracking', 5)->nullable();
            $table->string('status', 5)->nullable();
            $table->unique(['forwarder_code', 'event'], 'forwarder_event_unique');
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
        Schema::connection('shipntracking')->dropIfExists('stop_tracking_show_events');
    }
}
