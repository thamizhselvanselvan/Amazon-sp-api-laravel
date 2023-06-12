<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOutwardingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('outwardings');

        Schema::connection('shipntracking')->create('outwardings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mode')->comment('source-destination')->nullable();;
            $table->foreign('mode')->references('id')->on('process_masters');
            $table->string('awb_number')->nullable();
            $table->string('order_id')->nullable();
            $table->string('purchase_tracking_id')->nullable();

            $table->unsignedBigInteger('forwarder_2')->nullable();
            $table->foreign('forwarder_2')->references('id')->on('partners');
            $table->string('forwarder_2_awb')->nullable();

            $table->timestamps();

            $table->index(['awb_number'], 'awb_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->create('outwardings', function (Blueprint $table) {
            $table->id();
            $table->string('manifest_id')->nullable();
            $table->string('mode', 100)->nullable()->comment('source-destination');
            $table->tinyInteger('type')->nullable()->comment('1-source outward,2-destination outward');
            $table->string('awb_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();


            $table->unique(['mode', 'awb_number', 'status'], 'mode_awb_number_status_unique');
            $table->index(['awb_number'], 'awb_index');
            Schema::connection('shipntracking')->dropIfExists('outwardings');
        });
    }
}
