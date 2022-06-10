<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSheleveIdToShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('shelves', function (Blueprint $table) {
            $table->string('shelve_id')->after('rack_id');
        });
        Schema::connection('inventory')->table('bins', function (Blueprint $table) {
            $table->string('bin_id')->after('shelve_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shelves', function (Blueprint $table) {
            $table->dropColumn('shelve_id');
        });
        Schema::connection('inventory')->table('bins', function (Blueprint $table) {
            $table->dropColumn('bin_id');
        });
    }
}
