<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRemoveColumnsInShipementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
            $table->dropColumn('asin');
            $table->dropColumn('item_name');
            $table->dropColumn('price');
            $table->dropColumn('quantity');

            $table->text('items')->after('ship_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
            $table->string('asin')->after('ship_id');
            $table->string('item_name')->after('asin');
            $table->string('price')->after('item_name');
            $table->string('quantity')->after('price');

            $table->dropColumn('items');
        });
    }
}
