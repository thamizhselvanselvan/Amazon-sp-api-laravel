<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogUssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['catalogaes', 'catalogins', 'catalogsas', 'cataloguss'];
        foreach ($tables as $table) {

            Schema::connection('catalog')->create($table, function (Blueprint $table) {
                $table->id();
                $table->string('asin', 255)->nullable()->index();
                $table->string('source', 10)->nullable();
                $table->double('length')->nullable();
                $table->double('width')->nullable();
                $table->double('height')->nullable();
                $table->string('unit', 191)->nullable();
                $table->double('weight')->nullable();
                $table->string('weight_unit', 191)->nullable();
                $table->string('classification_id')->nullable();
                $table->string('brand', 191)->nullable();
                $table->string('manufacturer', 191)->nullable();
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
        $tables = ['catalogaes', 'catalogins', 'catalogsas', 'cataloguss'];
        foreach ($tables as $table) {

            Schema::connection('catalog')->dropIfExists($table);
        }
    }
}
