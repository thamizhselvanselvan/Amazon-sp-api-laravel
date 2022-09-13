<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogMissingAsinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('catalog_missing_asins', function (Blueprint $table) {
            $table->id();
            $table->string('asin', 25)->unique('asin_unique');
            $table->string('user_id', 10)->nullable();
            $table->string('source', 10)->nullable();
            $table->string('status', 10)->default(0);
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
        Schema::connection('catalog')->dropIfExists('catalog_missing_asins');
    }
}
