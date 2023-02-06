<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['catalognewaes', 'catalognewins', 'catalognewuss'];
        foreach ($tables as $table) {

            Schema::connection('catalog')->create($table, function (Blueprint $table) {
                $table->id();
                $table->string('asin', 191)->nullable()->index();
                $table->integer('seller_id')->nullable()->index();
                $table->string('source', 15)->nullable();
                $table->text('attributes')->nullable();
                $table->double('height')->nullable();
                $table->string('unit', 191)->nullable();
                $table->double('length')->nullable();
                $table->double('weight')->nullable();
                $table->string('weight_unit', 191)->nullable();
                $table->double('width', 191)->nullable();
                $table->text('images')->nullable();
                $table->string('product_types', 191)->nullable();
                $table->string('marketplace', 191)->nullable();
                $table->string('brand', 191)->nullable();
                $table->string('browse_classification', 191)->nullable();
                $table->text('color')->nullable();
                $table->string('item_classification', 191)->nullable();
                $table->text('item_name')->nullable();
                $table->string('manufacturer', 191)->nullable();
                $table->string('model_number', 191)->nullable();
                $table->integer('package_quantity')->nullable();
                $table->string('part_number', 191)->nullable();
                $table->string('size', 191)->nullable();
                $table->string('website_display_group', 191)->nullable();
                $table->string('style', 191)->nullable();
                $table->text('dimensions')->nullable();
                $table->unique(['asin'], 'asin_unique');
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
        $tables = ['catalognewaes', 'catalognewins', 'catalognewuss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->dropIfExists($table);
        }
    }
}
