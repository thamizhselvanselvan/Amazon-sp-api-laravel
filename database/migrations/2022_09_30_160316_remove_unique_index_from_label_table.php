<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueIndexFromLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels', function (Blueprint $table) {

            $table->dropUnique('order_awb_no_unique');
            $table->unique('order_no', 'order_awb_no_unique');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labels', function (Blueprint $table) {
            $table->dropUnique('order_awb_no_unique');
            $table->unique('order_no', 'order_awb_no_unique');
        });
    }
}
