<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddbbColumnsIntoPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->string('next_highest_seller_price', 30)->nullable()->default(0)->after('ind_to_sa');
            $table->string('next_highest_seller_id', 100)->nullable()->default(0)->after('next_highest_seller_price');
            $table->string('next_lowest_seller_price', 30)->nullable()->default(0)->after('next_highest_seller_id');
            $table->string('next_lowest_seller_id', 100)->nullable()->default(0)->after('next_lowest_seller_price');
            $table->string('bb_winner_price', 30)->nullable()->default(0)->default(0)->after('next_lowest_seller_id');
            $table->string('bb_winner_id', 100)->nullable()->default(0)->after('bb_winner_price');
            $table->string('is_any_our_seller_won_bb', 10)->nullable()->default(0)->after('bb_winner_id')->comment('0 = bb_lost, 1 = bb_win');
        });

        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->string('next_highest_seller_price', 30)->nullable()->default(0)->after('usa_to_sg');
            $table->string('next_highest_seller_id', 100)->nullable()->default(0)->after('next_highest_seller_price');
            $table->string('next_lowest_seller_price', 30)->nullable()->default(0)->after('next_highest_seller_id');
            $table->string('next_lowest_seller_id', 100)->nullable()->default(0)->after('next_lowest_seller_price');
            $table->string('bb_winner_price', 30)->nullable()->default(0)->after('next_lowest_seller_id');
            $table->string('bb_winner_id', 100)->nullable()->default(0)->after('bb_winner_price');
            $table->string('is_any_our_seller_won_bb', 10)->nullable()->default(0)->after('bb_winner_id')->comment('0 = bb_lost, 1 = bb_win');
        });

        Schema::connection('catalog')->table('pricing_aes', function (Blueprint $table) {
            $table->string('next_highest_seller_price', 30)->nullable()->default(0)->after('ae_price');
            $table->string('next_highest_seller_id', 100)->nullable()->default(0)->after('next_highest_seller_price');
            $table->string('next_lowest_seller_price', 30)->nullable()->default(0)->after('next_highest_seller_id');
            $table->string('next_lowest_seller_id', 100)->nullable()->default(0)->after('next_lowest_seller_price');
            $table->string('bb_winner_price', 30)->nullable()->default(0)->after('next_lowest_seller_id');
            $table->string('bb_winner_id', 100)->nullable()->default(0)->after('bb_winner_price');
            $table->string('is_any_our_seller_won_bb', 10)->nullable()->default(0)->after('bb_winner_id')->comment('0 = bb_lost, 1 = bb_win');
        });

        Schema::connection('catalog')->table('pricing_sas', function (Blueprint $table) {
            $table->string('next_highest_seller_price', 30)->nullable()->default(0)->after('sa_price');
            $table->string('next_highest_seller_id', 100)->nullable()->default(0)->after('next_highest_seller_price');
            $table->string('next_lowest_seller_price', 30)->nullable()->default(0)->after('next_highest_seller_id');
            $table->string('next_lowest_seller_id', 100)->nullable()->default(0)->after('next_lowest_seller_price');
            $table->string('bb_winner_price', 30)->nullable()->default(0)->after('next_lowest_seller_id');
            $table->string('bb_winner_id', 100)->nullable()->default(0)->after('bb_winner_price');
            $table->string('is_any_our_seller_won_bb', 10)->nullable()->default(0)->after('bb_winner_id')->comment('0 = bb_lost, 1 = bb_win');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->dropColumn('next_highest_seller_price');
            $table->dropColumn('next_highest_seller_id');
            $table->dropColumn('next_lowest_seller_price');
            $table->dropColumn('next_lowest_seller_id');
            $table->dropColumn('bb_winner_price');
            $table->dropColumn('bb_winner_id');
            $table->dropColumn('is_any_our_seller_won_bb');
        });

        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->dropColumn('next_highest_seller_price');
            $table->dropColumn('next_highest_seller_id');
            $table->dropColumn('next_lowest_seller_price');
            $table->dropColumn('next_lowest_seller_id');
            $table->dropColumn('bb_winner_price');
            $table->dropColumn('bb_winner_id');
            $table->dropColumn('is_any_our_seller_won_bb');
        });

        Schema::connection('catalog')->table('pricing_aes', function (Blueprint $table) {
            $table->dropColumn('next_highest_seller_price');
            $table->dropColumn('next_highest_seller_id');
            $table->dropColumn('next_lowest_seller_price');
            $table->dropColumn('next_lowest_seller_id');
            $table->dropColumn('bb_winner_price');
            $table->dropColumn('bb_winner_id');
            $table->dropColumn('is_any_our_seller_won_bb');
        });

        Schema::connection('catalog')->table('pricing_sas', function (Blueprint $table) {
            $table->dropColumn('next_highest_seller_price');
            $table->dropColumn('next_highest_seller_id');
            $table->dropColumn('next_lowest_seller_price');
            $table->dropColumn('next_lowest_seller_id');
            $table->dropColumn('bb_winner_price');
            $table->dropColumn('bb_winner_id');
            $table->dropColumn('is_any_our_seller_won_bb');
        });
    }
}
