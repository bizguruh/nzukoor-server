<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHighestEarningCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('highest_earning_courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('revenue');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('highest_earning_courses');
    }
}
