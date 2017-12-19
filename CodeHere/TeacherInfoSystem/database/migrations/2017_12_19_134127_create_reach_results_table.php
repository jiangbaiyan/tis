<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReachResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reach_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('reach_course_id');
            $table->double('avg_grade');
            $table->double('course_result');
            $table->double('graduate_result');
            $table->string('year',4)->default('');
            $table->tinyInteger('term');
            $table->string('url');
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
        Schema::dropIfExists('reach_results');
    }
}
