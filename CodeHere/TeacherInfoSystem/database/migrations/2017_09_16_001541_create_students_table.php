<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userid',8)->nullable();
            $table->string('name',10)->nullable();
            $table->string('openid',100)->nullable();
            $table->string('sex',2)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('grade',4)->nullable();
            $table->string('major',20)->nullable();
            $table->string('class',1)->nullable();
            $table->string('class_num',8)->nullable();
            $table->integer('account_id')->nullable();
            $table->tinyInteger('is_open')->default(1)->nullable();
            $table->index('userid');
            $table->index('account_id');
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
        Schema::dropIfExists('students');
    }
}
