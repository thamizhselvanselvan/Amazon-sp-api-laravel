<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppliedRulesColumnToProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("buybox_stores")->table('product_push', function (Blueprint $table) {
            $table->text("applied_rules")->default(0)->after('latency');
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
            $table->dropColumn('applied_rules');
        });
    }
}
