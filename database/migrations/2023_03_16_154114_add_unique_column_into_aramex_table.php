<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueColumnIntoAramexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('aramex_trackings', function (Blueprint $table) {
            $table->string('account_id', 30)->after('id');
            $table->index('awbno', 'awbno_index');
            $table->dropUnique('awbno_update_date_time_unique');
            $table->unique(['awbno', 'update_date_time', 'update_description'], 'awbno_update_date_time_description_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('aramex_trackings', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropIndex('awbno_index');
            $table->dropUnique('awbno_update_date_time_description_unique');
            $table->unique(['awbno', 'update_date_time'], 'awbno_update_date_time_unique');
        });
    }
}
