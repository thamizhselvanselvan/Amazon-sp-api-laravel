<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsinColumnToProductpushtable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->string('asin')->after('id');
            $table->string('push_status')->after('latency')->default(0)->comment("0 = pending, 1 = processed");
            $table->index('asin', 'asin_index');
            $table->index('push_status', 'push_status_index');
            $table->index('updated_at', 'updated_at_index');
          
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
   
    public function down()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->dropIndex('asin_index');
            $table->dropIndex('updated_at_index');
            $table->dropIndex('push_status_index');
            $table->dropColumn('asin');
            $table->dropColumn('push_status');
        });
    }
}
