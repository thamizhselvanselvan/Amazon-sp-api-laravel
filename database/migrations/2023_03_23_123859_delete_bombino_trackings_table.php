<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteBombinoTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('bombino_trackings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->create('bombino_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('awbno');
            $table->string('consignee')->nullable();
            $table->string('destination')->nullable();
            $table->string('forwarding_no')->nullable();
            $table->string('hawb_no')->nullable();
            $table->string('origin')->nullable();
            $table->dateTime('ship_date')->nullable();
            $table->string('status')->nullable();
            $table->string('weight')->nullable();
            $table->unique("awbno", "awbno_unique");
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
