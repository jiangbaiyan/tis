<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoAbroad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('go_abroad', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->tinyInteger('verify_level')->nullable()->default('0');
            $table->string('is_domestic')->nullable();
            $table->string('type')->nullable();
            $table->string('destination')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('start_stop_time')->nullable();
            $table->string('remark')->nullable();
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
