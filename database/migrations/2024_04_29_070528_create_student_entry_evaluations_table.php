<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentEntryEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_entry_evaluations', function (Blueprint $table) {
            $table->bigIncrements("entry_evaluation_id");
            $table->integer('student_id')->nullable();
            $table->string('word_test')->nullable();
            $table->string('word_test_date')->nullable();
            $table->string('book_level')->nullable();
            $table->string('hand2mind')->nullable();
            $table->string('hand2mind_date')->nullable();
            $table->string('which_hand2_mind_assessment')->nullable();
            $table->string('hand2mind_lesson')->nullable();
            $table->string('garfieldassessment')->nullable();
            $table->string('garfield_date')->nullable();
            $table->string('recreational_reading_raw_score')->nullable();
            $table->string('academice_reading_raw_score')->nullable();
            $table->string('full_scale_raw_score')->nullable();
            $table->string('gortassessment')->nullable();
            $table->string('gort_date')->nullable();
            $table->string('rate_row_score')->nullable();
            $table->string('accuracy_raw_score')->nullable();
            $table->string('fluency_raw_score')->nullable();
            $table->string('comprehension_raw_score')->nullable();
            $table->string('ctoppassessment')->nullable();
            $table->string('ctopp_date')->nullable();
            $table->string('ctopp_elison_raw_score')->nullable();
            $table->string('ctopp_blending_words_raw_score')->nullable();
            $table->string('ctopp_sound_matching_raw_score')->nullable();
            $table->string('ctopp_phoneme_isolation_raw_score')->nullable();
            $table->string('ctopp_memory_for_digit_raw_score')->nullable();
            $table->string('ctopp_nonword_repetition_raw_score')->nullable();
            $table->string('ctopp_rapid_digit_naming_raw_score')->nullable();
            $table->string('ctopp_rapid_letter_naming_raw_score')->nullable();
            $table->string('ctopp_rapid_color_naming_raw_score')->nullable();
            $table->string('ctopp_rapid_object_naming_raw_score')->nullable();
            $table->string('ctopp_blending_nonwords_raw_score')->nullable();
            $table->string('ctopp_segmenting_nonwords_raw_score')->nullable();
            $table->string('observation')->nullable();
            $table->string('observation_survey_date')->nullable();
            $table->string('observation_entry_letter')->nullable();
            $table->string('observation_entry_word')->nullable();
            $table->string('observation_entry_concept')->nullable();
            $table->string('observation_entry_writing')->nullable();
            $table->string('observation_entry_hearing')->nullable();
            $table->string('observation_entry_text_reading')->nullable();
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
        Schema::dropIfExists('student_entry_evaluations');
    }
}
