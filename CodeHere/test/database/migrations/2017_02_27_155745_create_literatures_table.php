<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiteraturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('literatures', function (Blueprint $table) {
            $table->increments('id');
            $table->string("author")->nullable();
            $table->string("literature_name")->nullable();
            $table->string("publisher_name")->nullable();
            $table->dateTime("publish_time")->nullable();
            $table->string("publisher_type")->nullable();
            $table->string("literature_honor")->nullable();
            $table->string("ISBN")->nullable();
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
        Schema::dropIfExists('literatures');
    }
}
