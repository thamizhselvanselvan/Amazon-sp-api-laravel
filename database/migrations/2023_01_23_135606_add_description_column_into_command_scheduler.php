<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnIntoCommandScheduler extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('command_schedulers', function (Blueprint $table) {

            $table->string('description', 255)->nullable()->after('execution_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('command_schedulers', function (Blueprint $table) {

            $table->dropColumn('description');
        });
    }
}
