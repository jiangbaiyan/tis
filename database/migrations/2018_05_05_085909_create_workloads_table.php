<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workloads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('姓名');
            $table->mediumInteger('year')->nullable()->comment('学年');
            $table->tinyInteger('term')->nullable()->comment('学年');
            $table->float('totalHour')->nullable()->comment('标准课时');
            $table->float('workload')->nullable()->comment('工作量');
            $table->string('md5')->nullable()->comment('md5');
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
        Schema::dropIfExists('workloads');
    }
}
