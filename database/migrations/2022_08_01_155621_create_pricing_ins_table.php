<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('pricing_ins', function (Blueprint $table) {
            $table->id();
            $table->string('asin', 20);
            $table->string('in_price', 20)->nullable();
            $table->string('weight', 20)->nullable()->comment('kg');
            $table->string('uae_sp', 20)->nullable();
            $table->string('sg_sp', 20)->nullable()->comment('singapore');
            $table->string('sa_sp', 20)->nullable()->comment('Saudi');
            $table->dateTime('price_updated_at')->nullable();
            $table->unique('asin', 'asin_unique');
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
        Schema::connection('catalog')->dropIfExists('pricing_ins');
    }
}
