<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForwrderMappingDetails extends Migration
{
    private $table_name = ['tracking_ins', 'tracking_ksa', 'tracking_aes'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->table_name as $table) {
            Schema::connection('shipntracking')->table($table, function (Blueprint $table) {

                $table->renameColumn('awb_number', 'id');
                $table->string('awb_no')->after('reference_id');
                $table->renameColumn('consignor', 'consignor_details');
                $table->renameColumn('consignee', 'consignee_details');
                $table->string('packet_details', 1000)->after('consignee')->nullable();
                $table->string('shipping_deails', 2000)->after('packet_details')->nullable();
                $table->string('booking_deails', 2000)->after('shipping_deails')->nullable();
                $table->string('purchase_tracking_id')->after('status')->nullable();

              
                
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
        foreach ($this->table_name as $table) {
            Schema::connection('shipntracking')->table($table, function (Blueprint $table) {
                $table->renameColumn('id', 'awb_number');
                $table->dropColumn('awb_no');
                $table->renameColumn('consignor_details', 'consignor');
                $table->renameColumn('consignee_details', 'consignee');
                $table->dropColumn('packet_details');
                $table->dropColumn('shipping_deails');
                $table->dropColumn('booking_deails');
                $table->dropColumn('purchase_tracking_id');

          
            });
        }
    }
}
