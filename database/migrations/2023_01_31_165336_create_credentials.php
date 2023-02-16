<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('store_name', 255);
            $table->string('merchant_id', 255);
            $table->string('authcode', 255);
            $table->integer('region_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('credentials');
    }
}
