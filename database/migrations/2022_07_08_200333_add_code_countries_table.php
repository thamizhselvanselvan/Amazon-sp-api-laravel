<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('countries', function (Blueprint $table) {
            $table->string('country_code')->after('name');
            $table->string('code')->after('country_code');
            $table->string('numeric_code')->after('code');
            $table->string('phone_code')->after('numeric_code');
            $table->string('capital')->after('phone_code');
            $table->string('currency')->after('capital');
            $table->string('currency_name')->after('currency');
            $table->string('currency_symbol')->after('currency_name');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::connection('inventory')->table('countries', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('code');
            $table->dropColumn('numeric_code');
            $table->dropColumn('phone_code');
            $table->dropColumn('capital');
            $table->dropColumn('currency');
            $table->dropColumn('currency_name');
            $table->dropColumn('currency_symbol');
        });
    }
}
