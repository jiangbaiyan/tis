<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->string('leave_reason')->nullable();
            $table->tinyInteger('is_leave')->default(0)->nullable();
            $table->string('where')->nullable();
            $table->date('cancel_date')->nullable();
            $table->tinyInteger('is_pass')->default(0)->nullable();
            $table->string('pass_reason')->nullable();
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
        Schema::dropIfExists('daily_leaves');
    }
}
