<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->string('cohort_id')->nullable();
            $table->bigInteger('university_id')->nullable();
            $table->bigInteger('school_id')->nullable();
            $table->bigInteger('teacher_id')->nullable();
            $table->bigInteger('tutor_id')->nullable();
            $table->string('student_name')->nullable();
            $table->integer('dcps_id')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('wave', ['1', '2', '3', '4', '5'])->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('grade', ['Kindergarten','1st Grade', '2nd Grade','3rd Grade','4th Grade','5th Grade'])->nullable();
            $table->enum('hispanic_latino', ['Yes','No'])->nullable();
            $table->string('race')->nullable();
            $table->string('other_race')->nullable();
            $table->enum('language', ['Yes','No'])->nullable();
            $table->string('nlanguage')->nullable();
            $table->string('otherlanguage')->nullable();
            $table->enum('mealcost', ['Free','Reduced', 'Regular'])->nullable();
            $table->enum('disability', ['Yes','No'])->nullable();
            $table->string('documented_disability')->nullable();
            $table->string('other_disability')->nullable();
            $table->enum('iep', ['Yes','No'])->nullable();
            $table->enum('project_participant', ['Yes','No'])->nullable();
            $table->string('last_year_id')->nullable();
            $table->string('comment')->nullable();
            $table->enum('status', ['active','inactive'])->nullable();
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
        Schema::dropIfExists('students');
    }
}
