<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\Glide\Manipulators\Blur;

class AddModeColumnIntoTrackingTable extends Migration
{
    public $tables = ['tracking_aes', 'tracking_ksa', 'tracking_ins'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::connection('shipntracking')->table($table, function (Blueprint $table) {

                $table->unsignedBigInteger('mode')->after('awb_no')->nullable();
                $table->foreign('mode')->references('id')->on('process_masters');
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
        foreach ($this->tables as $table) {
            Schema::connection('shipntracking')->table($table, function (Blueprint $table) {
                $table->dropForeign(['mode']);
                $table->dropColumn('mode');
            });
        }
    }
}
