<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePricingAesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->create('pricing_aes', function (Blueprint $table) {
            $this->tableStructure($table, 'ae_price');
        });

        Schema::connection('catalog')->create('pricing_sas', function (Blueprint $table) {
            $this->tableStructure($table, 'sa_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->dropIfExists('pricing_aes');
        Schema::connection('catalog')->dropIfExists('pricing_sas');
    }

    public function tableStructure($table, $custom_column_name)
    {
        $table->id();
        $table->string('asin', 20);
        $table->string('available', 5)->nullable();
        $table->string('weight', 255)->nullable();
        $table->string($custom_column_name, 255)->nullable();
        $table->dateTime('price_updated_at')->nullable();
        $table->unique('asin', 'unique_asin');
        $table->index('asin', 'index_asin');
        $table->softDeletes();
        $table->timestamps();
    }
}
