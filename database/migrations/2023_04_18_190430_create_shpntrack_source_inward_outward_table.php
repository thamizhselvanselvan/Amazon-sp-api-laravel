<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShpntrackSourceInwardOutwardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('inwardings', function (Blueprint $table) {
            $table->id();
            $table->string('manifest_id')->nullable();
            $table->string('mode',100)->nullable()->comment('source-destination');
            $table->tinyInteger('type')->nullable()->comment('1-source inward,2-destination inward');
            $table->string('awb_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();


            $table->unique(['mode','awb_number', 'status'], 'mode_awb_number_status_unique');
            $table->index(['awb_number'],'awb_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('inwardings');
    }
}
