<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReachGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reach_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('reach_course_id');
            $table->string('userid',10)->default('');
            $table->string('name',10)->default('');
            $table->double('grade');
            $table->string('year',4)->default('');
            $table->tinyInteger('term');
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
        Schema::dropIfExists('reach_grades');
    }
}
