<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("buybox_stores")->table('product_push', function (Blueprint $table) {
            $table->string("app_360_price", 50)->default(0)->after('is_bb_won');
            $table->string("destination_bb_price", 50)->default(0)->after('app_360_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("buybox_stores")->table('product_push', function (Blueprint $table) {
            $table->dropColumn('app_360_price');
            $table->dropColumn('destination_bb_price');
        });
    }
}
