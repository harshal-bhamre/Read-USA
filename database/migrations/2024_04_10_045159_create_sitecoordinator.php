<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\sitecoordinator;
class CreateSitecoordinator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sitecoordinator', function (Blueprint $table) {
            $table->bigIncrements('university_id');
            $table->string('university_name')->nullable();
            $table->string('enrollment')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('details_id')->nullable();
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
        Schema::dropIfExists('sitecoordinator');
    }
}
