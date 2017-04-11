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
        //Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 320);
            $table->string('name', 64);
            $table->string('username', 64);
            $table->string('email', 64);
            $table->string('address', 320);
            $table->integer('postcode');
            $table->string('town', 64);
            $table->string('country', 64);
            $table->string('activity', 32);
            $table->string('password', 64);
            $table->string('log_id', 64); 
            $table->integer('admin');

            // required for Laravel 4.1.26
            $table->string('remember_token', 100)->nullable();
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
