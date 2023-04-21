<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoShipntrackLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('labels', function ($table) {
            $table->unsignedBigInteger('mode')->after('order_no');
            $table->foreign('mode')->references('id')->on('label_masters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('labels', function ($table) {
            $table->dropForeign('labels_mode_foreign');
            $table->dropColumn('mode');
        });
    }
}
