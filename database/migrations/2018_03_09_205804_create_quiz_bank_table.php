<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizTypes', function (Blueprint $table) {
            $table->char('name', 20);
            $table->primary('name');
        });

        DB::table('quizTypes')->insert([
            ['name' => 'vocabulary obj'],
            ['name' => 'vocabulary sub'],
            ['name' => 'word obj'],
            ['name' => 'word sub'],
            ['name' => 'grammar obj'],
            ['name' => 'grammar sub']
        ]);

        Schema::create('quizBanks', function (Blueprint $table) {
            $table->increments('number');

	        $table->unsignedInteger('bookNumber')->nullable();
	        $table->foreign('bookNumber')->references('number')->on('books');

	        $table->unsignedSmallInteger('page')->nullable();

            $table->string('question',1000);
            $table->string('hint',100)->nullable();
	        $table->string('rightAnswer',100);
	        $table->string('example1',100)->nullable();
	        $table->string('example2',100)->nullable();
	        $table->string('example3',100)->nullable();

            $table->char('type', 20);
	        $table->foreign('type')->references('name')->on('quizTypes');

            $table->char('level', 1)->nullable();

	        $table->unsignedInteger('teacherNumber')->nullable();
	        $table->foreign('teacherNumber')->references('number')->on('users');
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
