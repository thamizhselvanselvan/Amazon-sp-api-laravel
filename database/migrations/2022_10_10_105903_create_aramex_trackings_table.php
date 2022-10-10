<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAramexTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('aramex_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('awbno', 20);
            $table->string('update_code', 20)->nullable();
            $table->string('update_description')->nullable();
            $table->dateTime('update_date_time')->nullable();
            $table->string('update_location')->nullable();
            $table->text('comment')->nullable();
            $table->string('gross_weight')->nullable();
            $table->string('chargeable_weight')->nullable();
            $table->string('weight_unit', 10)->nullable();
            $table->string('problem_code', 10)->nullable();
            $table->unique(['awbno', 'update_date_time'], 'awbno_update_date_time_unique');
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
        Schema::connection('shipntracking')->dropIfExists('aramex_trackings');
    }
}
