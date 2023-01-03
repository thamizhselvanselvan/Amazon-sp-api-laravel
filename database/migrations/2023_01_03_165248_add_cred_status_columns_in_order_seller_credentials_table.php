<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCredStatusColumnsInOrderSellerCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
            $table->string('cred_status', 10)->nullable()->after('store_name')->default('1')->comment('0 Not Working, 1 Working');
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
            $table->dropColumn('cred_status');
        });
    }
}
