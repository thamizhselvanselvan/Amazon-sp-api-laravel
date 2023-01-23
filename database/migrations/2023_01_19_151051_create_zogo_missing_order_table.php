<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZogoMissingOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->create('zogo_missing', function (Blueprint $table) {
            $table->id();
            $table->string('asin')->nullable();
            $table->string('amazon_order_id')->nullable();
            $table->string('order_item_id')->nullable();
            $table->string('price')->nullable()->default(0);
            $table->string('status')->nullable()->default(0)->comment("0 = not updated, 1 = updated");
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
        Schema::connection('order')->dropIfExists('zogo_missing');
    }
}
