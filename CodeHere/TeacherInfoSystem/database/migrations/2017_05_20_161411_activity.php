<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Activity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('name')->nullable();
            $table->tinyInteger('verify_level')->nullable()->default('0');
            $table->string('activity_type')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('total_members')->nullable();
            $table->string('activity_place')->nullable();
            $table->string('activity_time')->nullable();
            $table->string('abroad_members')->nullable();
            $table->string('science_core_index')->nullable();
            $table->text('remark')->nullable();
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
