<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_counters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->references('id')->on('users');
            $table->integer('follower')->default(0);
            $table->integer('friend')->default(0);
            $table->integer('zed')->default(0);
            $table->integer('zed_comment')->default(0);
            $table->integer('zed_like')->default(0);
            $table->integer('zed_dislike')->default(0);
            $table->integer('zed_correct')->default(0);
            $table->integer('zed_lie')->default(0);
            $table->integer('post')->default(0);
            $table->integer('post_comment')->default(0);
            $table->integer('post_like')->default(0);
            $table->integer('post_dislike')->default(0);
            $table->integer('post_vote')->default(0);
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
        Schema::dropIfExists('user_counters');
    }
}
