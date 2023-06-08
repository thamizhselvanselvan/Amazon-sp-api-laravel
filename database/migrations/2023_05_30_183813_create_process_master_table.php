<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('process_masters', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable();
            $table->string('destination')->nullable();
            $table->string('process_id')->nullable();
            $table->timestamps();

            $table->unique(['process_id', ], 'process_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('process_masters');
    }
}
