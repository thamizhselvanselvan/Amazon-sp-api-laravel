<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteCourierPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->dropIfExists('courier_partners');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->create('courier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('source_destination')->nullable();
            $table->string('courier_code')->nullable();
            $table->string('active')->default(1)->nullable();
            $table->timestamps();
            $table->unique(['name', 'source_destination'], 'name_source_des_unique');
        });
    }
}
