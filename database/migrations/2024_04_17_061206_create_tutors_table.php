<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id('tutor_id');
            $table->bigInteger('school_id')->nullable();
            $table->bigInteger('teacher_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('details_id')->nullable();
            $table->string('university_attended')->nullable();
            $table->string('zip_code')->nullable();
            $table->date('tutoring_start_date')->nullable();
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
        Schema::dropIfExists('tutors');
    }
}
