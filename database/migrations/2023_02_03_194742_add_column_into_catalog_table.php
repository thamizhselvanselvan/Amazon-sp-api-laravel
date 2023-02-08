<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoCatalogTable extends Migration
{
    private $catalog_tables = ['catalognewaes', 'catalognewins', 'catalognewsas', 'catalognewuss'];
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        foreach ($this->catalog_tables as $catalog_table) {

            Schema::connection('catalog')->table($catalog_table, function (Blueprint $table) {
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

        foreach ($this->catalog_tables as $catalog_table) {

            Schema::connection('catalog')->table($catalog_table, function (Blueprint $table) {
                $table->dropColumn('identifiers');
                $table->dropColumn('relationships');
                $table->dropColumn('salesRanks');
            });

        }
    }
}
