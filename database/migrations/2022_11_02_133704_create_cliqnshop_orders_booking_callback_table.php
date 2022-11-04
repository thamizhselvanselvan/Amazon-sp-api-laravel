<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCliqnshopOrdersBookingCallbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('business')->create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('xml_sent', 9000)->nullable();
            $table->string('sent_payload')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('order_date')->nullable();
            $table->string('name')->nullable();
            $table->string('e-mail')->nullable();
            $table->string('country_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('order_id')->nullable();
            $table->string('item_details', 1000)->nullable();
            $table->string('ship_address')->nullable();
            $table->string('bill_address')->nullable();
            $table->string('responce_payload')->nullable();
            $table->string('responce_text')->nullable();
            $table->string('responce_code')->nullable();
            
         
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
        Schema::connection('business')->dropIfExists('orders');
      
    }
}
