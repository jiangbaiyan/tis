<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('uid')->default('0')->comment('教师工号');
            $table->string('name',32)->default('')->comment('教师姓名');
            $table->string('openid',32)->default('')->comment('微信openid');
            $table->unsignedTinyInteger('sex')->default('0')->comment('教师性别,0-未知 1-男 2-女');
            $table->string('unit',32)->default('')->comment('学院');
            $table->string('email', 32)->default('')->comment('邮箱');
            $table->string('phone',11)->default('')->comment('手机号');
            $table->timestamps();
            $table->unique('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher');
    }
}
