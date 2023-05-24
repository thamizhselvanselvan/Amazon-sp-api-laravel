<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterZohoMissingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('us_price_missing', function (Blueprint $table) {
  
            $table->string('country_code', 5)->nullable()->after("id");
            $table->string("title", 1000)->nullable()->after("country_Code");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('us_price_missing', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('title');
        });
    }
}
