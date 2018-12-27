<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->references('id')->on('users');
            $table->smallInteger('post_type')->default(0);  // 0 = Inga , 1 = Reply
            $table->smallInteger('post_id')->default(0); // 0 = Only Post , 1 = Reply for post id
            $table->string('image')->nullable();
            $table->smallInteger('post_content_type')->default(0); // 0 = Text , 1 = Image , 2 = Video
            $table->string('video_website')->nullable();
            $table->string('video_id')->nullable();
            $table->string('content',500);
            $table->string('hashtags',100)->nullable();
            $table->integer('like_count')->default(0);
            $table->integer('dislike_count')->default(0);
            $table->integer('score_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->decimal('score_average',2,1)->default(0.0);
            $table->boolean('share_location')->default(false);
            $table->string('address',100)->nullable();
            $table->string('latitude',30)->nullable();
            $table->string('longitude',30)->nullable();
            $table->integer('reference_user_id')->references('id')->on('users');
            $table->boolean('comment_is_on')->default(1);
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('is_deleted_date')->nullable();
            $table->dateTime('post_order_date')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
