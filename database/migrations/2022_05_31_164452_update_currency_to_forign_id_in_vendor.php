<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCurrencyToForignIdInVendor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('vendors', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->foreignId('currency_id')->after('city'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('vendors', function (Blueprint $table) {
            $table->string('currency');
           $table->dropColumn('currency_id');
 
         });
    }
}
