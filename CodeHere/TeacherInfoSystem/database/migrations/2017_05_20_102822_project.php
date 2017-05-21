<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Project extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('name')->nullable();
            $table->tinyInteger('verify_level')->nullable()->default('0');
            $table->string('project_direction')->nullable();
            $table->string('project_name')->nullable();
            $table->string('project_members')->nullable();
            $table->string('project_number')->nullable();
            $table->string('project_type')->nullable();
            $table->string('project_level')->nullable();
            $table->date('project_build_time')->nullable();
            $table->string('start_stop_time')->nullable();
            $table->string('total_money')->nullable();
            $table->string('current_money')->nullable();
            $table->string('year_money')->nullable();
            $table->string('author_rank')->nullable();
            $table->string('author_task')->nullable();
            $table->string('science_core_index')->nullable();
            $table->text('remark')->nullable();
            $table->string('icon_path')->nullable();
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
        //
    }
}
