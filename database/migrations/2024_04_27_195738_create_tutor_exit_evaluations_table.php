<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorExitEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutor_exit_evaluations', function (Blueprint $table) {
            $table->bigIncrements('tutorexit_evaluation_id');
            $table->enum('gort', ['yes', 'no'])->nullable();
            $table->date('gort_assessment')->nullable();
            $table->integer('rate_raw_score')->nullable();
            $table->integer('accuracy_raw_score')->nullable();
            $table->integer('fluency_raw_score')->nullable();
            $table->integer('comprehension_raw_score')->nullable();
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
        Schema::dropIfExists('tutor_exit_evaluations');
    }
}
