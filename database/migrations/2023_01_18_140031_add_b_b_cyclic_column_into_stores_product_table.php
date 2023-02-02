<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBBCyclicColumnIntoStoresProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('products', function (Blueprint $table) {

            $table->string('bb_cyclic')->nullable()->after('cyclic')->default(0)->comment("0 = pending, 1 = Processed, 5 = processing");
            $table->string('priority')->nullable()->change();
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
            $table->dropColumn('bb_cyclic');
            $table->string('priority')->default(0)->change();
        });
    }
}
