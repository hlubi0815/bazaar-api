<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('phone');
            $table->tinyInteger('confirmed')->default(0); // this column will be a TINYINT with a default value of 0 , [0 for false & 1 for true i.e. verified]
            $table->string('confirmation_code')->nullable(); // this column will be a VARCHAR with no default value and will also BE NULLABLE
            $table->tinyInteger('data_deletion')->default(0);
            $table->tinyInteger('data_usage')->default(0);
            $table->tinyInteger('supporter')->nullable();
            $table->tinyInteger('next_bazaar')->nullable()->default(0);
            $table->tinyInteger('registration_channel');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
