<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patents', function (Blueprint $table) {
            $table->increments('id');
            $table->string("user");
            $table->string("proposer")->nullable();
            $table->string("patent_name")->nullable();
            $table->string("type")->nullable();
            $table->string("application_number")->nullable();
            $table->dateTime("apply_time")->nullable();
            $table->dateTime("authorization_time")->nullable();
            $table->string("certificate_number")->nullable();
            $table->string("patentee")->nullable();
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
        Schema::dropIfExists('patents');
    }
}
