<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueColumnIntoRatemasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('rate_masters', function (Blueprint $table) {
            $table->unique(['weight', 'source_destination'], 'unique_weight_source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_masters', function (Blueprint $table) {
            $table->dropUnique('unique_weight_source');
        });
    }
}
