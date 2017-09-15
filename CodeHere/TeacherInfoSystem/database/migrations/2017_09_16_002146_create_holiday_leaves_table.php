<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidayLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holiday_leaves', function (Blueprint $table) {
            $table->increments('id')->nullable();
            $table->integer('student_id')->nullable();
            $table->date('leave_time')->nullable();
            $table->string('where')->nullable();
            $table->date('back_time')->nullable();
            $table->date('cancel_time')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->timestamps();
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holiday_leaves');
    }
}
