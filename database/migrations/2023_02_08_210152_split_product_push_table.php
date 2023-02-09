<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SplitProductPushTable extends Migration
{
    private $table_name = ['products_push_ins', 'products_push_aes', 'products_push_sas'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->table_name as $name) {

            Schema::connection('buybox_stores')->create($name, function (Blueprint $table) {
                $table->id();
                $table->string('asin');
                $table->string('product_sku', 100);
                $table->string('store_id', 10);
                $table->tinyInteger('availability')->default(0);
                $table->string('current_store_price', 50)->default(0);
                $table->string('lowest_seller_id', 100)->default(0);
                $table->string('lowest_seller_price', 50)->default(0);
                $table->string('highest_seller_id', 100)->default(0);
                $table->string('highest_seller_price', 50)->default(0);
                $table->string('bb_winner_price', 50)->default(0);
                $table->string('bb_winner_id', 100)->default(0);
                $table->tinyInteger('is_bb_won')->default(0);
                $table->string("app_360_price", 50)->default(0);
                $table->string("destination_bb_price", 50)->default(0);
                $table->string('push_price', 100);
                $table->string('base_price', 100);
                $table->string('ceil_price', 50)->default(0);
                $table->string('latency', 100);
                $table->text("applied_rules")->nullable();
                $table->string('push_status')->default(0)->comment("0 = pending, 1 = processed");
                $table->string('feedback_price_id', 100)->nullable();
                $table->text('feedback_response')->nullable();
                $table->tinyInteger('feedback_price_status')->default(0)->comment("0 = not processed ,5 = processing ,1 = successful ,2 = failure");
                $table->string('feedback_availability_id', 100)->nullable();
                $table->timestamps();
                
                $table->index('asin', 'asin_index');
                $table->index('push_status', 'push_status_index');
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
        foreach ($this->table_name as $name) {
            Schema::connection('buybox_stores')->dropIfExists($name);
        }
    }
}
