<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvaliableColumnInPricingUssAndPricingInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->renameColumn('ind_sp', 'usa_to_in_b2b');
            $table->renameColumn('uae_sp', 'usa_to_uae');
            $table->renameColumn('sg_sp', 'usa_to_sg');
            $table->string('avaliable', 5)->after('asin')->nullable();
            $table->string('usa_to_in_b2c', 10)->after('ind_sp')->nullable();
        });

        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->renameColumn('uae_sp', 'ind_to_uae');
            $table->renameColumn('sg_sp', 'ind_to_sg');
            $table->renameColumn('sa_sp', 'ind_to_sa');
            $table->string('avaliable', 5)->after('asin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {

            $table->renameColumn('usa_to_in_b2b', 'ind_sp');
            $table->renameColumn('usa_to_uae', 'uae_sp');
            $table->renameColumn('usa_to_sg', 'sg_sp');
            $table->dropColumn('avaliable');
            $table->dropColumn('usa_to_in_b2c');
        });

        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->renameColumn('ind_to_uae', 'uae_sp');
            $table->renameColumn('ind_to_sg', 'sg_sp');
            $table->renameColumn('ind_to_sa', 'sa_sp');
            $table->dropColumn('avaliable');
        });
    }
}
