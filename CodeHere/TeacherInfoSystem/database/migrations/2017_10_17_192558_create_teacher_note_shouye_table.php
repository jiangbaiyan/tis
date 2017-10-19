<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherNoteShouyeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_note0_shouye', function (Blueprint $table) {
            $table->increments('id');
            $table->string('0_qishi');
            $table->string('0_jieshu');
            $table->string('0_xueqi');
            $table->string('0_xingming');
            $table->string('0_mingcheng');
            $table->string('0_kehao');
            $table->string('0_xingzhi');
            $table->string('0_duixiang');
            $table->dateTime('0_shijian');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_note_shouye');
    }
}
