<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->create('product_push', function (Blueprint $table) {
            $table->id();
            $table->string('store_id', 10);
            $table->string('push_price', 100);
            $table->string('latency', 100);
            $table->string('feedback_id', 100)->nullable();
            $table->text('feedback_response')->nullable();
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
        Schema::connection('buybox_stores')->dropIfExists('product_push');
    }
}
