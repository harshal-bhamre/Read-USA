<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExitSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exit_surveys', function (Blueprint $table) {
            $table->id('exit_survey_id');
            $table->integer('student_id')->nullable();
            $table->enum('status', ['The school or parent decided to stop participation', 'The student reached grade level reading proficiency'])->nullable();
            $table->string('comment')->nullable();
            $table->date('endate')->nullable();
            $table->integer('sessions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_surveys');
    }
}
