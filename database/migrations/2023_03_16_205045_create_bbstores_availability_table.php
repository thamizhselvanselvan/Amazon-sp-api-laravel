<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbstoresAvailabilityTable extends Migration
{
    private $table_names = ['product_availability_ins', 'product_availability_aes', 'product_availability_sas'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->table_names as $table_name) {

            Schema::create($table_name, function (Blueprint $table) {
                $table->id();
                $table->string('store_id', 5);
                $table->string('asin', 50);
                $table->string('product_sku', 50);
                $table->tinyInteger('current_availability')->comment("0 = inactive, 1 = active");
                $table->tinyInteger('push_availability')->comment("0 = inactive, 1 = active");
                $table->text('push_availability_reason')->nullable();
                $table->string('feedback_id', 100)->nullable();
                $table->text('feedback_response')->nullable();
                $table->tinyInteger('feedback_status')->default(0)->comment("0 = Not Processed, 5 = Processing, 1 = Successfull");
                $table->tinyInteger('push_status')->default(0)->comment('0 = not processed, 1 = pushed');
                $table->tinyInteger('export_status')->default(0)->comment("0 = Not exported, 1 = Exported");
                $table->timestamps();

                $table->index('asin', 'asin_index');
                $table->index('product_sku', 'product_sku_index');
                $table->index('store_id_sku', 'store_id_index');
                $table->index('push_status', 'push_status_index');
                $table->index('export_status', 'export_status_index');
                $table->index('updated_at', 'updated_at_index');
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
        foreach($this->table_names as $table_name) {

            Schema::dropIfExists($table_name);
        }
    }
}
