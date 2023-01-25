<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogAsinSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->dropIfExists('asin_masters');
        Schema::connection('catalog')->create('asin_sources', function (Blueprint $table) {
            $table->id();
            $table->string('asin', 20);
            $table->string('user_id', 10);
            $table->string('status', 5)->default(0);
            $table->string('source')->nullable();
            $table->unique(["user_id", "asin", "source"], 'user_asin_source_unique');

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
        Schema::connection('catalog')->dropIfExists('asin_sources');

        Schema::connection('catalog')->create('asin_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asin');
            $table->string('user_id')->nullable();
            $table->string('status')->default('0');
            $table->string('source')->nullable();
            $table->string('destination_1')->nullable();
            $table->string('destination_2')->nullable();
            $table->string('destination_3')->nullable();
            $table->string('destination_4')->nullable();
            $table->string('destination_5')->nullable();
            $table->string('source_price')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'asin', 'source'], 'user_asin_source_unique');
        });
    }
}
