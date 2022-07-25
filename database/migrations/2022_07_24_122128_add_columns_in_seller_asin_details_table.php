`<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class AddColumnsInSellerAsinDetailsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::connection('seller')->table('seller_asin_details', function (Blueprint $table) {

                $table->renameColumn('price', 'listingprice_amount');
                $table->string('delist', 5)->nullable()->after('asin');
                $table->string('is_buybox_winner', 5)->nullable()->after('is_fulfilment_by_amazon');
                $table->string('available', 5)->nullable()->after('status');
                $table->dateTime('price_updated_at')->nullable()->after('available');
                $table->string('status', 5)->change()->nullable();
                $table->string('source', 10)->nullable()->after('seller_id');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::connection('seller')->table('seller_asin_details', function (Blueprint $table) {
                $table->renameColumn('listingprice_amount', 'price');
                $table->dropColumn('is_buybox_winner');
                $table->dropColumn('delist');
                $table->dropColumn('available');
                $table->dropColumn('price_updated_at');
                $table->string('status')->change()->nullable();
                $table->dropColumn('source');
            });
        }
    }
