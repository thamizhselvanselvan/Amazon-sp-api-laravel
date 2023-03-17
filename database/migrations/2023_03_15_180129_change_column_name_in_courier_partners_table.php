<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameInCourierPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('courier_partners', function (Blueprint $table) {
            $table->string('type')->comment(' International 1, Domestic 2 or Both 3')
                ->change();
                $table->string('time_zone')->nullable()->after('courier_code');
           $table->unique(['courier_code'], 'courier_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('courier_partners', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
            $table->dropColumn('time_zone');
            $table->dropUnique('courier_code_unique');
        });
    }
}
