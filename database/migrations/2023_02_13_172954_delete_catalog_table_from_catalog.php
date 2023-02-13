<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteCatalogTableFromCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->dropIfExists('catalog');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->create('catalog', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->index('index_seller_id');
            $table->string('asin', 191)->nullable();
            $table->string('source', 191)->nullable();
            $table->string('binding', 191)->nullable();
            $table->string('brand', 191)->nullable();
            $table->string('color', 191)->nullable();
            $table->string('department', 191)->nullable();
            $table->string('display_size', 191)->nullable();
            $table->string('label', 191)->nullable();
            $table->string('manufacturer', 191)->nullable();
            $table->string('material_type', 191)->nullable();
            $table->string('package_dimensions', 191)->nullable();
            $table->integer('package_quantity')->nullable();
            $table->string('product_group', 191)->nullable();
            $table->string('product_type_name', 191)->nullable();
            $table->string('publisher', 191)->nullable();
            $table->string('size', 191)->nullable();
            $table->string('small_image', 191)->nullable();
            $table->string('studio', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->string('item_dimensions', 191)->nullable();
            $table->string('is_adult_product', 191)->nullable();
            $table->string('model', 191)->nullable();
            $table->string('part_number', 191)->nullable();
            $table->string('is_autographed', 191)->nullable();
            $table->string('is_memorabilia', 191)->nullable();
            $table->string('list_price', 191)->nullable();
            $table->integer('number_of_items')->nullable();
            $table->date('release_date')->nullable();
            $table->string('scent', 191)->nullable();
            $table->string('hardware_platform', 191)->nullable();
            $table->string('system_memory_size', 191)->nullable();
            $table->string('warranty', 191)->nullable();
            $table->string('flavor', 191)->nullable();
            $table->date('publication_date')->nullable();
            $table->string('manufacturer_minimum_age', 191)->nullable();
            $table->string('product_type_subcategory', 191)->nullable();
            $table->string('operating_system', 191)->nullable();
            $table->string('manufacturer_parts_warranty_description', 191)->nullable();
            $table->string('clasp_type', 191)->nullable();
            $table->string('creator', 191)->nullable();
            $table->string('languages', 191)->nullable();
            $table->string('platform', 191)->nullable();
            $table->string('system_memory_type', 191)->nullable();
            $table->string('artist', 191)->nullable();
            $table->string('genre', 191)->nullable();
            $table->string('item_part_number', 191)->nullable();
            $table->string('manufacturer_maximum_age', 191)->nullable();
            $table->string('maximum_resolution', 191)->nullable();
            $table->string('optical_zoom', 191)->nullable();
            $table->string('gem_type', 191)->nullable();
            $table->string('metal_type', 191)->nullable();
            $table->string('hand_orientation', 191)->nullable();
            $table->string('band_material_type', 191)->nullable();
            $table->string('hazardous_material_type', 191)->nullable();
            $table->string('edition', 191)->nullable();
            $table->string('shaft_material', 191)->nullable();
            $table->tinyInteger('number_of_discs')->nullable();
            $table->string('format', 191)->nullable();
            $table->string('metal_stamp', 191)->nullable();
            $table->string('hard_disk_interface', 191)->nullable();
            $table->string('hard_disk_size', 191)->nullable();
            $table->tinyInteger('processor_count')->nullable();
            $table->string('pegi_rating', 191)->nullable();
            $table->string('author', 191)->nullable();
            $table->string('aspect_ration', 191)->nullable();
            $table->string('golf_club_flex', 191)->nullable();
            $table->string('golf_club_loft', 191)->nullable();
            $table->integer('ring_size')->nullable();
            $table->string('chain_type', 191)->nullable();
            $table->string('size_per_pearl', 191)->nullable();
            $table->string('total_gem_weight', 191)->nullable();
        });
    }
}
