<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInfoTypeINFileManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_managements', function (Blueprint $table) {

            $table->text('info')->change();
            $table->text('header')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_managements', function (Blueprint $table) {

            $table->string('info', 1000)->change();
            $table->string('header', 1000)->change();
        });
    }
}
