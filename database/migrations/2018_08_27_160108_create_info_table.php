<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',512)->default('')->comment('通知标题');
            $table->string('content',1024)->default('')->comment('通知内容');
            $table->integer('uid')->default('0')->comment('通知接收人学号/工号');
            $table->string('name',32)->default('')->comment('通知接收人姓名');
            $table->unsignedTinyInteger('type')->default('0')->comment('通知对象类型|1-给全体本科生|2-给年级|3-给班级|4-给特定学生|5-给全体研究生|6-给研究生年级|7-给特定研究生|8-给全体教师|9-给特定教师');
            $table->unsignedTinyInteger('status')->default('0')->comment('是否查看|0-未查看|1-已查看');
            $table->string('attachment',512)->default('0')->comment('附件URL');
            $table->timestamp('time')->default('0')->comment('定时时间');
            $table->integer('teacher_id')->default('0')->comment('发通知教师id');
            $table->timestamps();
            $table->index('uid');
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info');
    }
}
