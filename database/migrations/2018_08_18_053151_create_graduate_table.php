<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGraduateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduate', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('uid')->default('0')->comment('学号');
            $table->string('name',32)->default('')->comment('学生姓名');
            $table->string('openid',32)->default('')->comment('微信openid');
            $table->unsignedTinyInteger('sex')->default('0')->comment('学生性别,0-未知 1-男 2-女');
            $table->string('phone',11)->default('')->comment('手机号');
            $table->string('email',32)->default('')->comment('邮箱');
            $table->string('unit',32)->default('')->comment('学院');
            $table->unsignedsmallInteger('grade')->default('0')->comment('年级');
            $table->unsignedInteger('teacher_id')->default('0')->comment('辅导员id');
            $table->unique('uid');
            $table->index('grade');
            $table->index('teacher_id');
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
        Schema::dropIfExists('graduate');
    }
}
