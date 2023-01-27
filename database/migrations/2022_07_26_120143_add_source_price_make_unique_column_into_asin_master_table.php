<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourcePriceMakeUniqueColumnIntoAsinMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('asin_masters', function (Blueprint $table) {
            $table->dropUnique('asin_masters_asin_unique');
            $table->string('source_price')->after('destination_5')->nullable();
            $table->unique(["user_id", "asin", "source"], 'user_asin_source_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('asin_masters', function (Blueprint $table) {
            $table->dropUnique('user_asin_source_unique');
            $table->dropColumn('source_price');
            $table->unique(["asin"], 'asin_masters_asin_unique');
        });
    }
}
