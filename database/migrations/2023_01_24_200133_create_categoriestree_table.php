<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriestreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('categoriestree', function (Blueprint $table) {
            $table->id();
            $table->string( 'browseNodeId' );
            $table->string( 'browseNodeName' );
            $table->string( 'browsePathId' );
            $table->string( 'Tree' );
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
        Schema::connection('catalog')->dropIfExists('categoriestree');
    }
}
