<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandSchedulerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('command_schedulers', function (Blueprint $table) {
            $table->id();
            $table->string('command_name', 255)->nullable();
            $table->string('execution_time', 60)->nullable();
            $table->integer('status')->nullable()->default(0)->comment('0 = disable, 1 = enable');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['command_name'], 'command_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('command_schedulers');
    }
}
