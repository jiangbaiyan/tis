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
            $table->date('date')->default('1971-01-01')->comment('请假课程日期');
            $table->unsignedTinyInteger('begin_course')->default('0')->comment('请假开始课程(1-12整数)');
            $table->unsignedTinyInteger('end_course')->default('0')->comment('请假结束课程(1-12整数)');
            $table->string('course_name',255)->default('0')->comment('课程名称');
            $table->string('teacher_name',64)->default('')->comment('任课教师姓名');
            $table->string('teacher_phone',11)->default('')->comment('任课教师手机号');
            $table->integer('daily_leave_id')->default('0')->comment('请假id');
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
