<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameIntoBombinoTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('bombino_trackings', function (Blueprint $table) {
            $table->renameColumn('awb_no', 'awbno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('bombino_trackings', function (Blueprint $table) {
            $table->renameColumn('awbno', 'awb_no');
        });
    }
}
