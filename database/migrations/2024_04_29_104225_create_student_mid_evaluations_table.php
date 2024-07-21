<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentMidEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_mid_evaluations', function (Blueprint $table) {
            $table->id("mid_evaluation_id");
            $table->integer('student_id')->nullable();
            $table->string('gort_assessed')->nullable();
            $table->string('gort_assessment_date')->nullable();
            $table->string('rate_raw_score')->nullable();
            $table->string('accuracy_raw_score')->nullable();
            $table->string('fluency_raw_score')->nullable();
            $table->string('comprehension_raw_score')->nullable();
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
        Schema::dropIfExists('student_mid_evaluations');
    }
}
