<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

class AddCredentialColumnIntoPartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->table('partners', function ($table) {

            $table->string('login_user', 255)->nullable()->after('key2');
            $table->string('login_email', 255)->nullable()->after('login_user');
            $table->unique(['courier_id', 'source', 'destination', 'login_email'], 'courier_source_destination_login_email_unique');
            $table->dropUnique('courier_id_source_des_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->table('partners', function ($table) {

            $table->DropColumn('login_user');
            $table->DropColumn('login_email');
            $table->unique(['courier_id', 'source', 'destination'], 'courier_id_source_des_unique');
            $table->dropUnique('courier_source_destination_login_email_unique');
        });
    }
}
