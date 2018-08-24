<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessionDatas', function (Blueprint $table) {
            $table->increments('number');

            $table->unsignedInteger('userNumber');
	        $table->foreign('userNumber')->references('number')->on('users');
	        $table->unique('userNumber');

            $table->unsignedInteger('raceNumber')->nullable();
	        $table->foreign('raceNumber')->references('number')->on('races');

            $table->unsignedInteger('characterNumber')->nullable();
	        $table->foreign('characterNumber')->references('number')->on('characters');
            $table->unique(['raceNumber', 'characterNumber']);

            $table->string('nick', 20)->nullable();

            $table->string('PIN',10)->nullable();
            $table->unique(['nick', 'PIN']);

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
