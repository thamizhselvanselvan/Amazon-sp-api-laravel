<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsPriceMissingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->create('us_price_missing', function (Blueprint $table) {
            $table->id();
            $table->string('asin')->nullable();
            $table->string('amazon_order_id')->nullable();
            $table->string('order_item_id')->nullable();
            $table->string('price')->nullable();
            $table->tinyInteger('status')->nullable()->comment('0-pending,1-updated');
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
        Schema::connection('order')->dropIfExists('us_price_missing');
    }
}
