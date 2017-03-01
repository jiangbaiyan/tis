<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoAbordInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goAbordInfos', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name")->nullable();
            $table->string("go_abord_type")->nullable();
            $table->string("destination")->nullable();
            $table->string("institution_name")->nullable();
            $table->dateTime("start_time")->nullable();
            $table->dateTime("stop_time")->nullable();
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
        Schema::dropIfExists('goAbordInfos');
    }
}
