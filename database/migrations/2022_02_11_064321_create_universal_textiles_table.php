<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniversalTextilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universal_textiles', function (Blueprint $table) {
            $table->id();
            $table->string('textile_id')->nullable();
            $table->string('ean')->nullable();
            $table->string('brand')->nullable();
            $table->string('title')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('transfer_price')->nullable();
            $table->string('shipping_weight')->nullable();
            $table->string('product_type')->nullable();
            $table->string('quantity')->nullable();
            $table->unique('textile_id', 'textile_id_unique_index');
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
        Schema::dropIfExists('universal_textiles');
    }
}
