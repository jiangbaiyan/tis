<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidayLeaveModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holiday_leave_model', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title','64')->default('')->comment('节假日模板标题');
            $table->date('from')->default('1971-01-01')->comment('节假日开始日期');
            $table->date('to')->default('1971-01-01')->comment('节假日结束日期');
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
        Schema::dropIfExists('holiday_leave_model');
    }
}
