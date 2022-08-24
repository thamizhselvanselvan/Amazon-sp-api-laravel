<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->create('business_orders', function (Blueprint $table) {
            $table->id();
            $table->string('sent_payload');
            $table->string('organization_name');
            $table->string('order_date');
            $table->string('name');
            $table->string('e-mail');
            $table->string('country_name');
            $table->string('country_code');
            $table->string('order_id');
            $table->string('item_details');
            $table->string('ship_address');
            $table->string('bill_address');
            $table->string('responce_payload');
            $table->string('responce_text');
            $table->string('responce_code');
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
        Schema::connection('order')->dropIfExists('business_orders');
    }
}
