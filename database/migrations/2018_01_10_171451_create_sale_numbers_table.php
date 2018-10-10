<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bazaar_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('sale_number')->unsigned();
            $table->unique(['bazaar_id', 'sale_number']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bazaar_id')->references('id')->on('bazaar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_numbers');
    }
}
