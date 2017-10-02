<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_content', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',50)->default('');
            $table->text('content')->nullable();
            $table->integer('account_id')->nullable();
            $table->string('attach_url',255)->default('');
            $table->tinyInteger('type')->comment('1-年级 2-班级 3-专业 4-特定学生')->default(0);
            $table->string('send_to',255)->default('');
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
        Schema::dropIfExists('info_content');
    }
}
