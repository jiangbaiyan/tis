<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holdConferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name")->nullable();
            $table->string("conference_name")->nullable();
            $table->string("conference_type")->nullable();
            $table->string("conference_address")->nullable();
            $table->dateTime("start_time")->nullable();
            $table->dateTime("stop_time")->nullable();
            $table->integer("headcount")->nullable();
            $table->integer("overseas_headcount")->nullable();
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
        Schema::dropIfExists('holdConferences');
    }
}
