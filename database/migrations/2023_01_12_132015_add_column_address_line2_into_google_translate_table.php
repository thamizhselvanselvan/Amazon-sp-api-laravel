<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAddressLine2IntoGoogleTranslateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_translates', function (Blueprint $table) {
            $table->renameColumn('address', 'addressline1');
            $table->string('addressline2', 255)->after('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_translates', function (Blueprint $table) {
            $table->renameColumn('addressline1', 'address');
            $table->dropColumn('addressline2');
        });
    }
}
