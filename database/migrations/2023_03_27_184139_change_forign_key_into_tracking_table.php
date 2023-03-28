<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForignKeyIntoTrackingTable extends Migration
{
    private $tables = ['tracking_aes', 'tracking_ins'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table) {

            Schema::connection('shipntracking')->table($table, function (Blueprint $table) {

                $table->dropForeign(['forwarder_1']);
                $table->dropForeign(['forwarder_2']);
                $table->dropForeign(['forwarder_3']);
                $table->dropForeign(['forwarder_4']);

                $table->foreign('forwarder_1')->references('id')->on('partners');
                $table->foreign('forwarder_2')->references('id')->on('partners');
                $table->foreign('forwarder_3')->references('id')->on('partners');
                $table->foreign('forwarder_4')->references('id')->on('partners');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
