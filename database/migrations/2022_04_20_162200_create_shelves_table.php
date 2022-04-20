<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('in')->create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('Shelves_name');
            $table->string('No_of_Bins');
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
        Schema::connection('in')->dropIfExists('shelves');
    }
}
