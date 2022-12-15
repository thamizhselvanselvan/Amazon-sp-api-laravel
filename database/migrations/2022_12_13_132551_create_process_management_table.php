<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_managements', function (Blueprint $table) {
            $table->id();
            $table->string('module', 255)->nullable();
            $table->string('command_name', 500)->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('command_start_time', 255)->nullable()->default('0000-00-00 00:00:00');
            $table->string('command_end_time', 255)->nullable()->default('0000-00-00 00:00:00');
            $table->string('status', 10)->nullable()->default('0');
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
        Schema::dropIfExists('process_managements');
    }
}
