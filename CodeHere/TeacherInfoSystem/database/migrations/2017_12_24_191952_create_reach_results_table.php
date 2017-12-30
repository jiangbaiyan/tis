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
            $table->string('course_result')->default('');
            $table->string('graduate_result')->default('');
            $table->string('year',4)->default('');
            $table->tinyInteger('term');
            $table->string('url')->default('');
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
