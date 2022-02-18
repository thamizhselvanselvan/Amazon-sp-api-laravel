<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUniversalTextilePrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('universal_textiles', function (Blueprint $table) {

            $table->unique('textile_id', 'textile_id_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('universal_textiles', function (Blueprint $table) {


            $table->dropUnique('textile_id_unique_index');
        });
    }
}
