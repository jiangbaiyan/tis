<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HoldMeeting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold_meeting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('name')->nullable();
            $table->tinyInteger('verify_level')->nullable()->default('0');
            $table->tinyInteger('is_domestic')->nullable();
            $table->string('meeting_name')->nullable();
            $table->string('total_people')->nullable();
            $table->string('meeting_place')->nullable();
            $table->date('meeting_time')->nullable();
            $table->string('abroad_people')->nullable();
            $table->string('remark')->nullable();
            $table->string('icon_path')->nullable();
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
        //
    }
}
