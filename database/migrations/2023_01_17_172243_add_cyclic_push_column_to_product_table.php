<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCyclicPushColumnToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('products', function (Blueprint $table) {

            $table->string('cyclic_push')->default('0')->after('store_price')->comment("0 = pending|new, 1 = processed, 5 = processing ");;
            $table->renameColumn('is_bb_own', 'is_bb_won');
            $table->string('latency')->default(7)->change();
            $table->index('updated_at', 'updated_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->table('products', function (Blueprint $table) {

            $table->dropColumn('cyclic_push');
            $table->renameColumn('is_bb_won', 'is_bb_own');
            $table->string('latency', 25)->default(null)->change();
            $table->dropIndex('updated_at_index');
        });
    }
}
