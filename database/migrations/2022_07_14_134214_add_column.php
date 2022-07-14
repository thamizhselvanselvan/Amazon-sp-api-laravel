<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
           $table->string('enable_shipntrack')->after('get_order_item');
           $table->string('source_destination')->after('enable_shipntrack');
           
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
           $table->dropColumn('enable_shipntrack');
           $table->dropColumn('source_destination');
           
       });
    }
}
