<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_socials',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('social_name');
            $table->string('nick_name')->nullable();
            $table->string('social_id',50);
            $table->string('followers_count')->default(0);
            $table->string('friends_count')->default(0);
            $table->string('profile_image_url');
            $table->string('token')->nullable();
            $table->string('token_secret')->nullable();
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
        Schema::dropIfExists('user_socials');
    }
}
