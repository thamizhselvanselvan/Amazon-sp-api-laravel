<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOrderColumnFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('business')->dropIfExists('orders');
      
         
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('business')->create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('xml_sent', 10000);
            $table->string('sent_payload');
            $table->string('organization_name');
            $table->string('order_date');
            $table->string('name');
            $table->string('e-mail');
            $table->string('country_name');
            $table->string('country_code');
            $table->string('order_id');
            $table->string('item_details', 1000);
            $table->string('ship_address');
            $table->string('bill_address');
            $table->string('responce_payload');
            $table->string('responce_text');
            $table->string('responce_code');
            $table->timestamps();
        });
    }
}
