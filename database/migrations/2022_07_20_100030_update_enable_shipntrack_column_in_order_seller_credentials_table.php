<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnableShipntrackColumnInOrderSellerCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {

            $table->string('dump_order', 10)->nullable()->default(0)->change();
            $table->string('get_order_item', 10)->nullable()->default(0)->change();
            $table->string('enable_shipntrack', 10)->nullable()->default(0)->change();
            $table->string('source_destination', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {

            $table->string('dump_order')->nullable(false)->default(null)->change();
            $table->string('get_order_item')->nullable(false)->default(null)->change();
            $table->string('enable_shipntrack')->nullable(false)->default(null)->change();
            $table->string('source_destination')->nullable(false)->default(null)->change();
        });
    }
}
