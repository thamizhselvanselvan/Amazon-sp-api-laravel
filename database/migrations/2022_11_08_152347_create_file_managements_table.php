<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_managements', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 10)->nullable();
            $table->string('type', 20)->nullable();
            $table->string('module', 50)->nullable();
            $table->string('file_name', 100)->nullable();
            $table->string('file_path', 1000)->nullable();
            $table->string('command_name', 255)->nullable();
            $table->string('command_start_time')->nullable()->default('0000-00-00 00:00:00');
            $table->string('command_end_time')->nullable()->default('0000-00-00 00:00:00');
            $table->string('status', 10)->nullable()->default(0);
            $table->string('info', 1000)->nullable();
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
        Schema::dropIfExists('file_managements');
    }
}
