<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInwardingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('inwardings');
        Schema::connection('shipntracking')->create('inwardings', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_id')->nullable();
            $table->string('total_items_in_export')->nullable();
            $table->string('total_items_receved')->nullable();
            $table->string('international_awb_number')->comment('Forwarder 1 awb')->nullable();
            $table->timestamps();


            $table->unique(['shipment_id'], 'shipment_id_unique');
            $table->index(['international_awb_number'], 'awb_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->create('inwardings', function (Blueprint $table) {
            $table->id();
            $table->string('manifest_id')->nullable();
            $table->string('mode', 100)->nullable()->comment('source-destination');
            $table->tinyInteger('type')->nullable()->comment('1-source inward,2-destination inward');
            $table->string('awb_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();


            $table->unique(['mode', 'awb_number', 'status'], 'mode_awb_number_status_unique');
            $table->index(['awb_number'], 'awb_index');
            Schema::connection('shipntracking')->dropIfExists('inwardings');
        });
    }
}
