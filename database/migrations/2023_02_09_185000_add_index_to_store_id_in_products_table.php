<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToStoreIdInProductsTable extends Migration
{
    private $tables = [
        "products_ins",
        "products_aes",
        "products_sas",

    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->tables as $table_name) {

            Schema::connection("buybox_stores")->table($table_name, function (Blueprint $table) {
                $table->index("store_id", "store_id_index");
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

        foreach($this->tables as $table_name) {

            Schema::connection("buybox_stores")->table($table_name, function (Blueprint $table) {
                $table->dropIndex("store_id_index");
            });

        }
 
    }
}
