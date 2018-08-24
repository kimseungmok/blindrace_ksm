<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // update 18.06.04
        Schema::dropIfExists('QnAs');

        // use 18.04.24
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('sessionDatas');
        Schema::dropIfExists('characters');

        // delete 18.04.24
        Schema::dropIfExists('playing_quizs');
        // update 18.04.24
        Schema::dropIfExists('records');
        // delete 18.04.24
        Schema::dropIfExists('mistaken_quizs');
        Schema::dropIfExists('race_mistaken_quizs');
        Schema::dropIfExists('race_set_exam_quizs');
        Schema::dropIfExists('race_quizs');

        // delete 18.04.24
        Schema::dropIfExists('race_team_users');
        //Schema::dropIfExists('race_teams');

        // delete 18.04.24
        Schema::dropIfExists('race_results');
        // update 18.04.24
        Schema::dropIfExists('raceUsers');
        // update 18.05.09
        Schema::dropIfExists('raceUserStates');
        // delete 18.05.09
        Schema::dropIfExists('retestStates');

        //delete 18.04.24
        Schema::dropIfExists('race_teams');

        // delete 18.04.24
        Schema::dropIfExists('rece_set_exam');
        Schema::dropIfExists('rece_set_exam_state_keyword');
        Schema::dropIfExists('race_set_exam');
        Schema::dropIfExists('race_set_exam_state_keyword');
        // update 18.04.24
        Schema::dropIfExists('races');
        Schema::dropIfExists('raceTypes');

        // update 18.04.24
        Schema::dropIfExists('listQuizs');

        // delete 18.04.24
        Schema::dropIfExists('quiz_bank');
        Schema::dropIfExists('quiz_type_keyword');
        Schema::dropIfExists('quiz_set_state_keyword');
        // update 18.04.24
        Schema::dropIfExists('quizBanks');
        Schema::dropIfExists('quizTypes');

        // use 18.04.24
        Schema::dropIfExists('books');

        // delete 18.04.24
	    DB::unprepared('DROP TRIGGER IF EXISTS tr_races_user_division_check');
        Schema::dropIfExists('races');
        Schema::dropIfExists('race_folders');
        // update 18.04.24
        Schema::dropIfExists('lists');
        Schema::dropIfExists('folders');

        // delete 18.04.24
        Schema::dropIfExists('group_students');
        Schema::dropIfExists('group_student_state_keyword');
        // update 18.04.24
        Schema::dropIfExists('groupStudents');
        Schema::dropIfExists('accessionStates');

        // use 18.04.24
	    DB::unprepared('DROP TRIGGER IF EXISTS tr_groups_user_division_check');
        Schema::dropIfExists('groups');

        // update 18.06.18
        Schema::dropIfExists('files');

        // delete 18.04.24
        Schema::dropIfExists('user_teachers');
        // update 18.04.24
        Schema::dropIfExists('users');
        Schema::dropIfExists('classifications');
        // delete 18.04.24
        Schema::dropIfExists('user_division_keyword');
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
