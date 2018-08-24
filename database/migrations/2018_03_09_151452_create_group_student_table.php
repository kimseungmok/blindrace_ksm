<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupStudents', function (Blueprint $table) {
	        $table->unsignedInteger('groupNumber');
	        $table->foreign('groupNumber')->references('number')->on('groups');

	        $table->unsignedInteger('userNumber');
	        $table->foreign('userNumber')->references('number')->on('users');
	        $table->primary(['groupNumber', 'userNumber']);

            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
