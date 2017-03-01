<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldAcademicCommunicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holdAcademicCommunications', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name")->nullable();
            $table->string("project_name")->nullable();
            $table->string("cooperative_partner")->nullable();
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
        Schema::dropIfExists('holdAcademicCommunications');
    }
}
