<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipntrackTrackingEventMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('tracking_event_emirates');
        Schema::connection('shipntracking')->dropIfExists('tracking_event_bombinos');
        Schema::connection('shipntracking')->dropIfExists('tracking_event_samsas');
        
        Schema::connection('shipntracking')->create('tracking_event_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('master_event_code', 50)->nullable();
            $table->string('source', 50)->nullable();
            $table->string('our_event_code', 50)->nullable();
            $table->string('our_event_description', 255)->nullable();
            $table->string('active',10)->nullable()->comment('0 = Inactive 1 = Active');
            $table->timestamps();
            $table->unique(['source', 'our_event_code'], 'event_code_source_unique');
            $table->foreign('master_event_code', 'master_event_code_foreign_key')->references('event_code')->on('tracking_event_masters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('tracking_event_mappings');
    }
}
