<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcademicPartTimeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academicPartTimeJobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string("duty")->nullable();
            $table->dateTime("start_time")->nullable();
            $table->dateTime("stop_time")->nullable();
            $table->string("institution_name")->nullable();
            $table->string("part_time_duty")->nullable();
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
        Schema::dropIfExists('academicPartTimeJobs');
    }
}
