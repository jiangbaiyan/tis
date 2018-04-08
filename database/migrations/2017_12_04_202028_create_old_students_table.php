<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userid',10)->default('');
            $table->string('name',10)->default('');
            $table->string('openid',100)->default('');
            $table->string('sex',2)->default('');
            $table->string('phone',20)->default('');
            $table->string('email',255)->default('');
            $table->string('unit',255)->default('');
            $table->string('grade',4)->default('');
            $table->integer('account_id')->nullable();
            $table->tinyInteger('is_open')->default(1);
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
        Schema::dropIfExists('old_students');
    }
}
