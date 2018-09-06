<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachReachStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teach_reach_state', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cg',1024)->default('')->comment('课程目标达成度');
            $table->string('gg',1024)->default('')->comment('毕业要求指标点达成度');
            $table->string('course_name')->default('')->comment('课程名称');
            $table->string('year',32)->default('')->comment('学年');
            $table->unsignedTinyInteger('term')->default('0')->commet('学期');
            $table->integer('teacher_id')->default('0')->comment('教师id');
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
        Schema::dropIfExists('teach_reach_state');
    }
}
