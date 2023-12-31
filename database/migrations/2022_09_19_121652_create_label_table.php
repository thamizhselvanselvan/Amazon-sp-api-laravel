<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('status', 10)->default(0);
            $table->string('order_no', 255)->nullable();
            $table->string('awb_no', 255)->nullable();
            $table->string('bag_no', 20)->nullable();
            $table->string('forwarder', 255)->nullable();
            $table->unique(['order_no', 'awb_no'], 'order_awb_no_unique');
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
        Schema::dropIfExists('labels');
    }
}
