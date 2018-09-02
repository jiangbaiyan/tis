<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyLeaveCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_leave_course', function (Blueprint $table) {
            $table->increments('id');
            $table->string('course_name',255)->default('')->comment('课程名称');
            $table->string('teacher_name',64)->default('')->comment('任课教师姓名');
            $table->string('teacher_phone',11)->default('')->comment('任课教师手机号');
            $table->unsignedInteger('daily_leave_id')->default('0')->comment('请假id');
            $table->index('daily_leave_id');
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
        Schema::dropIfExists('daily_leave_course');
    }
}
