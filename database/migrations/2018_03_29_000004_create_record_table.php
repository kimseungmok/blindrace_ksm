<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->unsignedInteger('raceNo');
	        $table->unsignedInteger('userNo');
            $table->foreign(['raceNo', 'userNo'])->references(['raceNumber', 'userNumber'])->on('raceUsers');

            $table->unsignedInteger('listNo');
            $table->unsignedInteger('quizNo');
            $table->foreign(['listNo', 'quizNo'])->references(['listNumber', 'quizNumber'])->on('listQuizs');

            $table->unsignedTinyInteger('retest')->default(0);
	        $table->primary(['raceNo', 'userNo', 'listNo', 'quizNo', 'retest']);

            $table->string('answer', 100);

            $table->string('answerCheck', 1);

            $table->text('wrongAnswerNote')->nullable();
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
