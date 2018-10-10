<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBazaarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bazaar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamp('bazaardate');
            $table->smallInteger('listnumber_start');
            $table->smallInteger('listnumber_end');
            $table->float('fee',3,2)->default(2.0);
            $table->tinyInteger('percentageoff')->default(20);
            $table->float('change');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bazaar');
    }
}
