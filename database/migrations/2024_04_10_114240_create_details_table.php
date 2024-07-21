<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Details', function (Blueprint $table) {
            $table->bigIncrements('details_id');
            $table->string('district')->nullable();
            $table->string('building')->nullable();
            $table->string('address')->nullable();
            $table->date('doa')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->enum('hispanic', ['1-Yes', '0-No'])->nullable();
            $table->string('race')->nullable();
            $table->string('other_race')->nullable();
            $table->string('trained')->nullable();
            $table->string('primary_role')->nullable();
            $table->integer('experience')->nullable();
            $table->string('highest_edu')->nullable();
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
        Schema::dropIfExists('details');
    }
}
