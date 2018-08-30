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
            $table->unsignedTinyInteger('is_leave_hz')->deafult('0')->comment('是否离杭');
            $table->string('destination',255)->default('')->comment('去往何处');
            $table->unsignedTinyInteger('status')->default('')->comment('辅导员审核状态|1-审核中|2-审核通过|3-审核不通过');
            $table->string('auth_reason',255)->default('')->comment('辅导员审批备注');
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
