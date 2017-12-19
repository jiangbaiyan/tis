<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReachCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reach_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reach_major_id');
            $table->integer('reach_point_id');
            $table->string('name')->default('');
            $table->string('step')->default('');
            $table->string('goal')->default('');
            $table->string('course_ratio')->default('');
            $table->string('graduate_ratio')->default('');
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
        Schema::dropIfExists('reach_courses');
    }
}
