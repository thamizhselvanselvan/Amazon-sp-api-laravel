<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('manifest_items', function (Blueprint $table) {
            $table->id();
     

            $table->string('manifest_id')->nullable();
            $table->string('awb')->nullable();
            $table->string('destination')->nullable();
            // $table->foreign('destination')->references('id')->on('process_masters');
            $table->string('inscan_manifest_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('purchase_tracking_id')->nullable();
            $table->unsignedBigInteger('forwarder_1')->nullable();
            $table->foreign('forwarder_1')->references('id')->on('partners');
            $table->string('forwarder_1_awb')->nullable();
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
        Schema::connection('shipntracking')->dropIfExists('manifest_items');
    }
}
