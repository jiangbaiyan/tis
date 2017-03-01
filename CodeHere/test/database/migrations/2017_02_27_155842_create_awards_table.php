<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name")->nullable();
            $table->string("achievement_name")->nullable();
            $table->string("award_name")->nullable();
            $table->string("award_rating")->nullable();
            $table->dateTime("award_time")->nullable();
            $table->string("certificate_number")->nullable();
            $table->string("prize_winner")->nullable();
            $table->string("identity_rating")->nullable();
            $table->string("remark")->nullable();
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
        Schema::dropIfExists('awards');
    }
}
