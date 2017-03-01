<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformsAndTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platformsAndTeams', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean("is_academy_host")->nullable();
            $table->string("platform_and_team_name")->nullable();
            $table->string("platform_and_team_rank")->nullable();
            $table->string("member_info")->nullable();
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
        Schema::dropIfExists('platformsAndTeams');
    }
}
