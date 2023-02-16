<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteAsinMasterTableFromCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->dropIfExists('asin_masters');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->create('asin_masters', function (Blueprint $table) {
            $table->id();
            $table->string('asin', 255);
            $table->string('user_id', 255)->nullable();
            $table->string('status', 255)->nullable()->default(0);
            $table->string('source')->nullable();
            $table->string('destination_1', 255)->nullable();
            $table->string('destination_2', 255)->nullable();
            $table->string('destination_3', 255)->nullable();
            $table->string('destination_4', 255)->nullable();
            $table->string('destination_5', 255)->nullable();
            $table->string('source_price', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['asin', 'user_id', 'source'], 'user_asin_source_unique');
        });
    }
}
