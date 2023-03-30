<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiDisplayColumnToStatusMasters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('status_master', function (Blueprint $table) {
            $table->tinyInteger('api_display')->after('stop_tracking')->default(1)->comment('0-Stop Showing on API,1-show on API');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('status_master', function (Blueprint $table) {
            $table->dropColumn('api_display');
        });
    }
}
