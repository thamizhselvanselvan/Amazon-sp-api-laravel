<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SplitSourceDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
            $table->renameColumn('source_destination', 'source');
            $table->string('destination')->after('source_destination');
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
            $table->renameColumn('source', 'source_destination');
            $table->dropColumn('destination');
        });
        
    }
}
