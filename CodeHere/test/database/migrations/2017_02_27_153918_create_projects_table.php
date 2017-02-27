<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string("level")->nullable();
            $table->string("personal_info")->nullable();
            $table->string("subject_categories")->nullable();
            $table->string("project_number")->nullable();
            $table->string("project_topic")->nullable();
            $table->string("expenditure_amount")->nullable();
            $table->string("project_principal")->nullable();
            $table->string("project_approval_time")->nullable();
            $table->string("research_expenditure")->nullable();
            $table->string("existing_money")->nullable();
            $table->string("total_expenditure")->nullable();
            $table->string("project_classify")->nullable();
            $table->boolean("is_audited")->default(false);
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
        Schema::dropIfExists('projects');
    }
}
