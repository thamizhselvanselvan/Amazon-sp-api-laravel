<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogTableForBusinessApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('business')->create('catalog_business', function (Blueprint $table) {
            $table->id();
            $table->string('asin');
            $table->string('asin_type');
            // $table->string('signedProductid');
            $table->string('availability');
            $table->string('buyingGuidance');
            $table->string('fulfillmentType');
            $table->string('merchant');
            // $table->string('offerid', 1000);
            $table->string('price');
            $table->string('listPrice');
            $table->string('productCondition');
            $table->string('condition');
            // $table->string('quantityLimits', 1000);
            $table->string('deliveryInformation');
            // $table->string('features', 1000);
            // $table->string('taxonomies', 1000);
            $table->string('title', 1000);
            $table->string('url');
            // $table->string('productOverview', 1000);
            // $table->string('productvariations', 1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('business')->dropIfExists('catalog_business');
    }
}
