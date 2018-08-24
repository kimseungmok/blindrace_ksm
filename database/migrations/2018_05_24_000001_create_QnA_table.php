<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQnATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('QnAs', function (Blueprint $table) {
            $table->increments('number');

            $table->unsignedInteger('userNumber');
            $table->foreign('userNumber')->references('number')->on('users');

            $table->unsignedInteger('teacherNumber');
            $table->foreign('teacherNumber')->references('number')->on('users');

            $table->unsignedInteger('groupNumber');
            $table->foreign('groupNumber')->references('number')->on('groups');

            $table->string('title', 50);
            $table->text('question');
            $table->text('answer')->nullable();

            $table->unsignedInteger('answerFileNumber')->nullable();
            $table->foreign('answerFileNumber')->references('number')->on('files');

            $table->unsignedInteger('questionFileNumber')->nullable();
            $table->foreign('questionFileNumber')->references('number')->on('files');

            $table->timestamp('question_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('answer_at')->nullable();
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
