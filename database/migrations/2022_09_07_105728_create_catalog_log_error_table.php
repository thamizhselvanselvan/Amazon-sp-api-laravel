<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogLogErrorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_reportings', function (Blueprint $table) {
            $table->id();
            $table->string('queue_type', 255)->nullable();
            $table->string('identifier', 255)->nullable();
            $table->string('identifier_type', 100)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('aws_key', 10)->nullable();
            $table->string('error_code', 25)->nullable();
            $table->text('message')->nullable();
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
        Schema::dropIfExists('error_reportings');
    }
}
