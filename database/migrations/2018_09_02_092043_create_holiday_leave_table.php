<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidayLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holiday_leave', function (Blueprint $table) {
            $table->increments('id');
            $table->string('destination',32)->default('')->comment('去往何处');
            $table->unsignedInteger('student_id')->default('0')->comment('登记学生id');
            $table->unsignedInteger('holiday_leave_model_id')->default('0')->comment('节假日请假模板id');
            $table->index('student_id');
            $table->index('holiday_leave_model_id');
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
        Schema::dropIfExists('holiday_leave');
    }
}
