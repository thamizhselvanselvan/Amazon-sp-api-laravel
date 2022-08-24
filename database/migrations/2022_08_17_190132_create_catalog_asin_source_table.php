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
    }
}
