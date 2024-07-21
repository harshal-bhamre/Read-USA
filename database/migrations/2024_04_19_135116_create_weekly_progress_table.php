<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_progress', function (Blueprint $table) {
            $table->id('weekly_progress');
            $table->integer('student_id')->nullable();
            $table->date('weekly')->nullable();
            $table->enum('fluency_this_week', ['Yes', 'No'])->nullable();
            $table->date('identification_fluency_probe')->nullable();
            $table->integer('fluency_score')->nullable();
            $table->enum('student_fluency_this_week', ['Yes', 'No'])->nullable();
            $table->date('students_fluency')->nullable();
            $table->string('book_level_fluency_rating', 1)->nullable();
            $table->string('title_of_the_book_fluency')->nullable();
            $table->integer('self_corrections_book')->nullable();
            $table->enum('students_accuracy_score1', ['<90', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99', '100'])->nullable();
            $table->integer('students_fluency_score')->nullable();
            $table->integer('student_comprehension_score_within')->nullable();
            $table->integer('student_comprehension_score_beyond_about')->nullable();
            $table->integer('total_comprehension_score1')->nullable();
            $table->enum('instructional_level', ['Independent', 'Instructional', 'Hard'])->nullable();
            $table->enum('current_book_level', ['Pre-A', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'])->nullable();
            $table->enum('attendance_monday', ['Full Lesson', 'Partial Lesson', 'No Lesson', 'No Tutoring', 'Assessments'])->nullable();
            $table->enum('attendance_tuesday', ['Full Lesson', 'Partial Lesson', 'No Lesson', 'No Tutoring', 'Assessments'])->nullable();
            $table->enum('attendance_wednesday', ['Full Lesson', 'Partial Lesson', 'No Lesson', 'No Tutoring', 'Assessments'])->nullable();
            $table->enum('attendance_thursday', ['Full Lesson', 'Partial Lesson', 'No Lesson', 'No Tutoring', 'Assessments'])->nullable();
            $table->enum('attendance_friday', ['Full Lesson', 'Partial Lesson', 'No Lesson', 'No Tutoring', 'Assessments'])->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('weekly_progress');
    }
}
