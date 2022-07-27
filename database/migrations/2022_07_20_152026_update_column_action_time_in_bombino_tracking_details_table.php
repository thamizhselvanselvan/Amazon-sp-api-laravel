<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnActionTimeInBombinoTrackingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('bombino_tracking_details', function (Blueprint $table) {
        
            $table->time('action_time')->change();
            $table->date('action_date')->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('bombino_tracking_details', function (Blueprint $table) {
            $table->dateTime('action_time')->change() ;
            $table->dateTime('action_date')->change() ;
        });
    }
}
