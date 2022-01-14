<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products',function (Blueprint $table){

            $table->string('brand');
            $table->string('product_group');
            $table->string('product_type_name');
            $table->string('publisher');
            $table->string('manufacturer');
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products',function(Blueprint $table){

            $table->dropColumn([
                'brand',
                'product_group',
                'product_type_name',
                'publisher',
                'manufacturer'
            ]);
        });
    }
}
