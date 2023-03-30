<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSmsaTableAccordingToDestination extends Migration
{
    public $destinations = ['ae', 'ksa'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->destinations as $destination) {

            Schema::connection('shipntracking')->dropIfExists($destination . '_smsa_trackings');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->destinations as $destination) {

            Schema::connection('shipntracking')->create($destination . '_smsa_trackings', function (Blueprint $table) {
                $table->id();
                $table->string('account_id', 20);
                $table->string('awbno')->index('awbno_index');
                $table->dateTime('date')->nullable();
                $table->string('activity')->nullable();
                $table->string('details')->nullable();
                $table->string('location')->nullable();
                $table->unique(["awbno", "date", "activity"], 'awbno_date_activity_unique');
                $table->timestamps();
            });
        }
    }
}
