<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThesisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thesis', function (Blueprint $table) {
            $table->increments('id');
            $table->string("user");
            $table->string("name")->nullable();
            $table->string("thesis_topic")->nullable();
            $table->string("periodical_or_conference")->nullable();
            $table->string("ISSN_or_ISBN")->nullable();
            $table->string("issue")->nullable();
            $table->string("volume")->nullable();
            $table->string("page_number")->nullable();
            $table->dateTime("publication_year")->nullable();
            $table->dateTime("publication_time")->nullable();
            $table->string("SCI")->nullable();
            $table->string("EI")->nullable();
            $table->string("CCF")->nullable();
            $table->string("is_include_by_domestic_periodical")->nullable();
            $table->string("accession_number")->nullable();
            $table->text("remark")->nullable();
            $table->string("author_rank")->nullable();
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
        Schema::dropIfExists('theses');
    }
}
