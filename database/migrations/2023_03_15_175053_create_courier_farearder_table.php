<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourierFarearderTable extends Migration
{
    private $table_name = ['process_india_to_uae', 'process_usa_to_uae', 'process_usa_to_ksa'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->table_name as $table) {

            Schema::connection('shipntracking')->create($table, function (Blueprint $table) {

                $table->bigIncrements('awb_number');
                $table->string('reference_id', 20);
                $table->string('consignor')->nullable();
                $table->string('consignee')->nullable();
                $table->unsignedBigInteger('forwarder_1')->nullable();
                $table->foreign('forwarder_1')->references('id')->on('courier_partners');
                $table->string('forwarder_1_awb', 20)->nullable();
                $table->string('forwarder_1_flag', 20)->nullable()->comment('0 Keep checking, 1 stop checking');

                $table->unsignedBigInteger('forwarder_2')->nullable();;
                $table->foreign('forwarder_2')->references('id')->on('courier_partners');
                $table->string('forwarder_2_awb', 20)->nullable();
                $table->string('forwarder_2_flag', 20)->nullable()->comment('0 Keep checking, 1 stop checking');;

                $table->unsignedBigInteger('forwarder_3')->nullable();;
                $table->foreign('forwarder_3')->references('id')->on('courier_partners');
                $table->string('forwarder_3_awb', 20)->nullable();
                $table->string('forwarder_3_flag', 20)->nullable()->comment('0 Keep checking, 1 stop checking');;

                $table->unsignedBigInteger('forwarder_4')->nullable();;
                $table->foreign('forwarder_4')->references('id')->on('courier_partners');
                $table->string('forwarder_4_awb', 20)->nullable();
                $table->string('forwarder_4_flag', 20)->nullable()->comment('0 Keep checking, 1 stop checking');;

                $table->string('status', 20)->nullable();
                $table->unique(['reference_id'], 'reference_id_unique');
                $table->timestamps();
            });
            DB::connection('shipntracking')->update("ALTER TABLE $table AUTO_INCREMENT = 1000000000;");
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

            Schema::connection('shipntracking')->dropIfExists($table);
        }
    }
}
