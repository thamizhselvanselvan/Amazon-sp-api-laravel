<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('in_scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination')->comment('mode')->nullable();
            $table->foreign('destination')->references('id')->on('process_masters');

            $table->string('manifest_id')->nullable();
            $table->string('awb_number')->nullable();
            $table->string('purchase_tracking_id')->index('tracking_id_index')->nullable();
            $table->string('order_id')->nullable();
            $table->string('export_status')->comment('o-pending,1-exported')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('in_scans');
    }
}
