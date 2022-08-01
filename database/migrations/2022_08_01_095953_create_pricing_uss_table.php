<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingUssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('pricing_uss', function (Blueprint $table) {
            $table->id();
            $table->string('asin', 20);
            $table->string('weight')->nullable()->comment('kg');
            $table->string('us_price', 20)->nullable();
            $table->string('ind_sp', 20)->nullable();
            $table->string('uae_sp', 20)->nullable();
            $table->string('sg_sp', 20)->nullable();
            $table->dateTime('price_updated_at')->nullable();
            $table->unique('asin', 'unique_asin');
            $table->softDeletes();
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
        Schema::connection('catalog')->dropIfExists('pricing_uss');
    }
}
