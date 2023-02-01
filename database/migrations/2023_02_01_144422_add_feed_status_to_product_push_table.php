<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeedStatusToProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->tinyInteger('feedback_price_status')->default(0)->after('feedback_response')->comment("0 = not processed ,5 = processing ,1 = successful ,2 = failure");
        });
        Schema::connection('order')->rename( 'zogo_missing', 'zoho_missing');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->dropColumn('feedback_price_status');
        });
        Schema::connection('order')->rename('zoho_missing', 'zogo_missing');
    }
}
