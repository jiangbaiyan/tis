<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDailyLeaveColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_leaves',function (Blueprint $table){
            $table->renameColumn('from','begin_time');
            $table->renameColumn('to','end_time');
        });

        Schema::table('holiday_leaves',function (Blueprint $table){
            $table->renameColumn('leave_time','begin_time');
            $table->renameColumn('back_time','end_time');
            $table->renameColumn('from','valid_from');
            $table->renameColumn('to','valid_to');
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
