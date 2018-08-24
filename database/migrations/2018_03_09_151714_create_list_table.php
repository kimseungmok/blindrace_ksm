<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->increments('number');

            $table->string('name', 100)->nullable();

            $table->unsignedInteger('teacherNumber');
            $table->foreign('teacherNumber')->references('number')->on('users');
        });

        Schema::create('lists', function (Blueprint $table) {
            $table->increments('number');

            $table->string('name', 100);

            $table->unsignedInteger('folderNumber')->nullable();
            $table->foreign('folderNumber')->references('number')->on('folders');

            $table->unsignedSmallInteger('openState')->default(1);

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
