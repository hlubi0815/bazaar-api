<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('receipt_id')->unsigned();
            $table->integer('sale_number')->unsigned();
            $table->decimal('amount', 8, 2);
            $table->integer('item_number')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('receipt_id')->references('id')->on('receipts')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_items');
    }
}
