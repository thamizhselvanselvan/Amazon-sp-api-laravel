<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSourceDestinationUniqueAsinUserid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin'], 'user_asin_unique');
            
        });

        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin'], 'user_asin_unique');
            
        });

        Schema::connection('catalog')->table('asin_source_ins', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin'], 'user_asin_unique');
            
        });

        Schema::connection('catalog')->table('asin_source_uss', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin'], 'user_asin_unique');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin','user_id'], 'user_asin_unique');
            
        });

        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin','user_id'], 'user_asin_unique');
            
        });

        Schema::connection('catalog')->table('asin_source_ins', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin','user_id'], 'user_asin_unique');
            
        });

        Schema::connection('catalog')->table('asin_source_uss', function (Blueprint $table) {
            $table->dropUnique('user_asin_unique');
            $table->unique(['asin','user_id'], 'user_asin_unique');
            
        });
    }
}
