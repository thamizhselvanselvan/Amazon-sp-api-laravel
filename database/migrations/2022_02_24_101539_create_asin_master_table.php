<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsinMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asin_masters', function (Blueprint $table) {
            $table->id();
            $table->string('asin')->unique();
            $table->string('source')->nullable();
            $table->string('destination_1')->nullable();
            $table->string('destination_2')->nullable();
            $table->string('destination_3')->nullable();
            $table->string('destination_4')->nullable();
            $table->string('destination_5')->nullable();
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
        Schema::dropIfExists('asin_masters');
    }
}
