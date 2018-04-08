<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HoldCommunication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold_communication', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->tinyInteger('verify_level')->nullable()->default('0');
            $table->string('is_domestic')->nullable();
            $table->string('communication_name')->nullable();
            $table->string('start_stop_time')->nullable();
            $table->string('work_object')->nullable();
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
