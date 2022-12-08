<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnCourierPartnerZohoIntoOrderSellerCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
            $table->string('courier_partner', 100)->nullable()->after('enable_shipntrack');
            $table->string('zoho', 10)->nullable()->default(0)->after('courier_partner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
            $table->dropColumn('courier_partner');
            $table->dropColumn('zoho');
        });
    }
}
