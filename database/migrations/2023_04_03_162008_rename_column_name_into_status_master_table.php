<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnNameIntoStatusMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('status_master', function (Blueprint $table) {
            $table->renameColumn('api_display', 'first_mile_status');
            $table->string('last_mile_status', 10)->default(1)->comment('0 - stop showing on API, 1 - show on API')->after('api_display');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('status_master', function (Blueprint $table) {
            $table->dropColumn('last_mile_status');
            $table->renameColumn('first_mile_status', 'api_display');
        });
    }
}
