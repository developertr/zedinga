<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPrivacyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_privacies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->smallInteger('follow_privacy')->default(1);  // 1 = Beni herkes takip edebilir. , 2 = Beni sadece takip ettiklerim takip edebilir. , 3 = Takip onay bekler
            $table->boolean('post_privacy')->default(false);
            $table->boolean('location_privacy')->default(true);
            $table->boolean('message_privacy')->default(true);
            $table->boolean('email_search_privacy')->default(true);
            $table->boolean('mobile_search_privacy')->default(true);
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
        Schema::dropIfExists('user_privacy');
    }
}
