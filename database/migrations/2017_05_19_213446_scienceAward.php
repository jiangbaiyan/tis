<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScienceAward extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scienceAwards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('name')->nullable();
            $table->tinyInteger('verify_level')->nullable()->default('0');
            $table->string('award_name')->nullable();
            $table->string('award_level')->nullable();
            $table->string('award_time')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('members_name')->nullable();
            $table->string('author_rank')->nullable();
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