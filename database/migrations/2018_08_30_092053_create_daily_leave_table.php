<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_leave', function (Blueprint $table) {
            $table->increments('id');
            $table->string('leave_reason',255)->default('')->comment('请假理由');
            $table->date('begin_time')->default('1971-01-01')->comment('请假开始日期');
            $table->date('end_time')->default('1971-01-01')->comment('请假结束日期');
            $table->unsignedTinyInteger('begin_course')->default('0')->comment('请假开始课程');
            $table->unsignedTinyInteger('end_course')->default('0')->comment('请假结束课程');
            $table->unsignedTinyInteger('is_leave_hz')->deafult('0')->comment('是否离杭');
            $table->string('destination',255)->default('')->comment('去往何处');
            $table->unsignedTinyInteger('status')->default('0')->comment('辅导员审核状态|1-审核中|2-审核通过|3-审核不通过');
            $table->string('auth_reason',255)->default('')->comment('辅导员审核备注');
            $table->unsignedInteger('teacher_id')->default('0')->comment('所属辅导员');
            $table->unsignedInteger('student_id')->default('0')->comment('请假发起人');
            $table->index('status');
            $table->index('teacher_id');
            $table->index('student_id');
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
        Schema::dropIfExists('daily_leave');
    }
}
