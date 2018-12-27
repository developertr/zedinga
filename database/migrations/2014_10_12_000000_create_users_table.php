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
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',60)->nullable();
            $table->string('name',100)->nullable();
            $table->string('mobile_number',20)->nullable();
            $table->string('latitude',25)->nullable();
            $table->string('longitude',25)->nullable();
            $table->smallInteger('profile_image_upload')->default(0);
            $table->string('profile_image')->nullable();
            $table->smallInteger('cover_image_upload')->default(0);
            $table->string('cover_image')->nullable();
            $table->decimal('score',2,1)->default(2.5);
            $table->string('description',150)->nullable();
            $table->smallInteger('activated')->default(0);
            $table->boolean('blocked')->default(false);
            $table->dateTime('blocked_date')->nullable();
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
