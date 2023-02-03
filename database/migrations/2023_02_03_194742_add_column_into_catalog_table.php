<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['catalognewaes', 'catalognewins', 'catalognewuss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->text('identifiers')->nullable()->after('dimensions');
                $table->text('relationships')->nullable()->after('identifiers');
                $table->text('salesRanks')->nullable()->after('relationships');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['catalognewaes', 'catalognewins', 'catalognewuss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->dropColumn('identifiers');
                $table->dropColumn('relationships');
                $table->dropColumn('salesRanks');
            });
        }
    }
}
