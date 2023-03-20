<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAramexTrackingViaDestinationTable extends Migration
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

            Schema::connection('shipntracking')->create($destination . '_aramex_trackings', function (Blueprint $table) {
                $table->id();
                $table->string('account_id', 20);
                $table->string('awbno', 20)->index('awbno_index');
                $table->string('update_code', 20)->nullable();
                $table->string('update_description')->nullable();
                $table->dateTime('update_date_time')->nullable();
                $table->string('update_location')->nullable();
                $table->text('comment')->nullable();
                $table->string('gross_weight')->nullable();
                $table->string('chargeable_weight')->nullable();
                $table->string('weight_unit', 10)->nullable();
                $table->string('problem_code', 10)->nullable();
                $table->unique(['awbno', 'update_date_time', 'update_description'], 'awbno_update_date_time_description_unique');
                $table->timestamps();
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
        foreach ($this->destinations as $destination) {

            Schema::connection('shipntracking')->dropIfExists($destination . '_aramex_trackings');
        }
    }
}
