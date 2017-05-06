<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string("user");
            $table->string("name")->nullable();
            $table->string("gender")->nullable();
            $table->date("birthday")->nullable();
            $table->string("politics_status")->nullable();
            $table->string("education")->nullable();
            $table->string("degree")->nullable();
            $table->string("professional_title")->nullable();
            $table->string("team")->nullable();
            $table->string("address")->nullable();
            $table->string("telephone")->nullable();
            $table->string("mobile_phone")->nullable();
            $table->string("email")->nullable();
            $table->text("edu_experience")->nullable();
            $table->text("work_experience")->nullable();
            $table->text("research")->nullable();
            $table->text("teaching_experience")->nullable();
            $table->text("achievement")->nullable();
            $table->string("icon_path")->nullable();
            $table->string("academy")->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
