<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAwbHistoryQtyQtyHistoryOrderItemIdInLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels', function (Blueprint $table) {

            $table->string('order_item_id', 50)->nullable()->after('order_no');
            $table->string('qty', 10)->nullable()->after('forwarder');
            $table->dropUnique('order_awb_no_unique');
            $table->unique(['order_no', 'order_item_id', 'bag_no'], 'order_item_bag_unique');
        });

        Schema::create('label_changes', function (Blueprint $table) {

            $table->string('order_no', 50)->nullable();
            $table->string('order_item_id', 50)->nullable();
            $table->string('awb_no', 50)->nullable();
            $table->string('inward_awb', 50)->nullable();
            $table->string('bag_no', 50)->nullable();
            $table->string('forwarder', 50)->nullable();

            $table->unique(['order_no', 'order_item_id', 'bag_no'], 'order_item_bag_unique');
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

            $table->dropColumn('order_item_id');
            $table->dropColumn('qty');
            $table->dropUnique('order_item_bag_unique');
            $table->unique(['order_no', 'bag_no'], 'order_awb_no_unique');
        });

        Schema::dropIfExists('label_changes');
    }
}
