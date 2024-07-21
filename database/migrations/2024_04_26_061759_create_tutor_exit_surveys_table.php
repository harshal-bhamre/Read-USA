<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorExitSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutor_exit_surveys', function (Blueprint $table) {
            $table->bigIncrements('exitsurvey_id');
            $table->enum('status', ['terminated', 'resigned'])->nullable();
            $table->date('last_lesson')->nullable();
            $table->integer('readusa_lessons')->nullable();
            $table->integer('tutor_id')->nullable();
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
        Schema::dropIfExists('tutor_exit_surveys');
    }
}
