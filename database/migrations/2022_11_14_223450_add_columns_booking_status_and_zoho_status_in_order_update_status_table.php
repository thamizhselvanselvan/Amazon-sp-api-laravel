<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsBookingStatusAndZohoStatusInOrderUpdateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_update_details', function (Blueprint $table) {

            $table->string('booking_status', 5)->default('0')->comment('1 booked, 5 Under processing, 0 Not processed')->after('courier_awb')->nullable();
            $table->string('zoho_status', 5)->default('0')->comment('1 booked, 5 Under processing, 0 Not processed')->after('zoho_id')->nullable();
            $table->dropColumn('zoho_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('order_update_details', function (Blueprint $table) {

            $table->dropColumn('booking_status');
            $table->dropColumn('zoho_status');
            $table->string('zoho_order_id')->nullable();
        });
    }
}
