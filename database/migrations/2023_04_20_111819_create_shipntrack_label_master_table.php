<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipntrackLabelMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('label_masters', function (Blueprint $table) {
            $table->id();
            $table->string('source', 10);
            $table->string('destination', 10);
            $table->string('file_path', 255)->nullable();
            $table->string('return_address', 500)->nullable();
            $table->unique(['source', 'destination'], 'source_destination_unique');
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
        Schema::connection('shipntracking')->dropIfExists('label_masters');
    }
}
