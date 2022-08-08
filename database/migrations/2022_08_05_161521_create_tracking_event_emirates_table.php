<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingEventEmiratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('tracking_event_emirates', function (Blueprint $table) {
            $table->id();
            $table->string('event_code', 50);
            $table->string('description', 255)->nullable();
            $table->string('active', 20)->nullable()->comment('0 = Inactive 1 = Active');
            $table->timestamps();
            $table->unique('event_code', 'emirate_event_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('tracking_event_emirates');
    }
}
