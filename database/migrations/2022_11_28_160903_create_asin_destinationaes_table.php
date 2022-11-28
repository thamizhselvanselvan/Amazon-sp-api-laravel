<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsinDestinationaesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('asin_destination_aes', function (Blueprint $table) {
            $this->destinationTableStructure($table);
        });

        Schema::connection('catalog')->create('asin_destination_sas', function (Blueprint $table) {
            $this->destinationTableStructure($table);
        });

        Schema::connection('catalog')->create('asin_source_sas', function (Blueprint $table) {
            $this->sourceTableStructure($table);
        });

        Schema::connection('catalog')->create('asin_source_aes', function (Blueprint $table) {
            $this->sourceTableStructure($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $drop_table = [
            'asin_destination_aes',
            'asin_destination_sas',
            'asin_source_aes',
            'asin_source_sas',
        ];

        foreach ($drop_table as $table) {
            Schema::connection('catalog')->dropIfExists($table);
        }
    }

    public function destinationTableStructure($table)
    {
        $table->id();
        $table->string('asin', 20);
        $table->string('user_id', 10)->nullable();
        $table->string('status', 5)->default('0')->nullable();
        $table->string('price_status', 10)->default('0')->nullable();
        $table->string('priority', 10)->nullable();
        $table->softDeletes();
        $table->timestamps();
    }

    public function sourceTableStructure($table)
    {
        $table->id();
        $table->string('asin', 20);
        $table->string('user_id', 10)->nullable();
        $table->string('status', 5)->default('0')->nullable();
        $table->softDeletes();
        $table->timestamps();
    }
}
