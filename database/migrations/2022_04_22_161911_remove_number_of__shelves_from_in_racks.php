<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNumberOfShelvesFromInRacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('in')->table('racks', function (Blueprint $table) {
            $table->dropColumn('number of Shelves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('in')->table('racks', function (Blueprint $table) {
            $table->string('number of Shelves');
        });
    }
}
