<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('courier', function (Blueprint $table) {
            $table->id();
            $table->string('courier_name')->nullable();
            $table->string('courier_code')->nullable();
            $table->timestamps();

            $table->unique(['courier_name', 'courier_code'], 'courier_name_code_unique');
        });
        Schema::connection('shipntracking')->dropIfExists('courier_partners');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('courier');
        Schema::connection('shipntracking')->create('courier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('source', 100)->nullable();
            $table->string('destination', 100)->nullable();
            $table->string('active', 100)->default(1)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('courier_code', 100)->nullable();
            $table->string('key1')->nullable();
            $table->string('key2')->nullable();
            $table->string('key3')->nullable();
            $table->string('key4')->nullable();
            $table->string('key5')->nullable();
            $table->timestamps();
            $table->unique(['name', 'source', 'destination'], 'name_source_des_unique');
        });
    }
}
