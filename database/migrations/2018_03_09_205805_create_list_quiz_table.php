<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listQuizs', function (Blueprint $table) {
            $table->unsignedInteger('listNumber')->nullable();
            $table->foreign('listNumber')->references('number')->on('lists');

            $table->unsignedInteger('quizNumber')->nullable();
            $table->foreign('quizNumber')->references('number')->on('quizBanks');
            $table->primary(['listNumber', 'quizNumber']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
