<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raceTypes', function (Blueprint $table) {
            $table->char('name', 20);
            $table->primary('name');
        });

        DB::table('raceTypes')->insert([
            ['name' => 'race'],
            ['name' => 'popQuiz']
        ]);

        Schema::create('races', function (Blueprint $table) {
            $table->increments('number');

	        $table->unsignedInteger('groupNumber');
            $table->foreign('groupNumber')->references('number')->on('groups');

            $table->unsignedInteger('listNumber');
	        $table->foreign('listNumber')->references('number')->on('lists');

            $table->unsignedInteger('teacherNumber');
            $table->foreign('teacherNumber')->references('number')->on('users');

	        $table->char('type', 20);
	        $table->foreign('type')->references('name')->on('raceTypes');

	        $table->unsignedTinyInteger('passingMark')->default(60);

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
