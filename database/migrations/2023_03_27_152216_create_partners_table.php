<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('shipntracking')->create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 100)->nullable();
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->foreign('courier_id')->references('id')->on('courier');

            $table->string('source', 100)->nullable();
            $table->string('destination', 100)->nullable();
            $table->string('active', 100)->default(1)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('time_zone', 100)->nullable();
            $table->string('user_id')->nullable();
            $table->string('password')->nullable();
            $table->string('account_id')->nullable();
            $table->string('key1')->nullable();
            $table->string('key2')->nullable();
            $table->timestamps();
            $table->unique(['courier_id', 'source', 'destination'], 'courier_id_source_des_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('partners');
    }
}
