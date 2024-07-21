<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorWeeklyProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutor_weekly_progress', function (Blueprint $table) {
            $table->bigIncrements('tutorweeklyprogress_id');
            $table->string('week')->nullable();
            $table->string('lessons')->nullable();
            $table->string('attendence_monday')->nullable();
            $table->string('attendence_tuesday')->nullable();
            $table->string('attendence_wednesday')->nullable();
            $table->string('attendence_thursday')->nullable();
            $table->string('attendence_friday')->nullable();
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
        Schema::dropIfExists('tutor_weekly_progress');
    }
}
