<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexAndColumnIntoSmsaTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('smsa_trackings', function (Blueprint $table) {
            $table->string('account_id', 30)->after('id');
            $table->index('awbno', 'awbno_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('smsa_trackings', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropIndex('awbno_index');
        });
    }
}
