<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendConferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name")->nullable();
            $table->string("conference_topic")->nullable();
            $table->string("conference_type")->nullable();
            $table->string("conference_address")->nullable();
            $table->dateTime("conference_time")->nullable();
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
        Schema::dropIfExists('attendConferences');
    }
}
