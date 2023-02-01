<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCeilColumnInProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->string('ceil_price', 50)->default(0)->after('base_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->dropColumn('ceil_price');
        });
    }
}
