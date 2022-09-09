<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceStatusInAsinDestinationInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->string('price_status', 10)->default('0')->nullable()->after('status');
        });
        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->string('price_status', 10)->default('0')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->dropColumn('price_status');
        });
        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->dropColumn('price_status');
        });
    }
}
