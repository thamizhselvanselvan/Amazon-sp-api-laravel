<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogExchangeRateForPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('source_destination', 50)->nullable();
            $table->string('base_weight', 15)->nullable();
            $table->string('base_shipping_charge', 10)->nullable();
            $table->string('packaging', 10)->nullable();
            $table->string('seller_commission', 10)->nullable();
            $table->string('duty_rate',10)->nullable();
            $table->string('sp_commission', 10)->nullable();
            $table->string('excerise_rate', 10)->nullable();
            $table->string('amazon_commission', 10)->nullable();
            $table->unique(['source_destination'], 'source_destination_unique');
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
        Schema::connection('catalog')->dropIfExists('exchange_rates');
    }
}
