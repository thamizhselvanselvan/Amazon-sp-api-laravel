<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnIntoProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->renameColumn('feedback_id', 'feedback_price_id');
            $table->string('feedback_availability_id', 100)->after('feedback_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->renameColumn('feedback_price_id', 'feedback_id');
            $table->dropColumn('feedback_availability_id');
        });
    }
}
