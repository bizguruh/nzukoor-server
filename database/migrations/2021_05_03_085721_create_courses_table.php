<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('knowledge_areas');
            $table->integer('curriculum');
            $table->string('modules')->nullable();
            $table->string('duration');
            $table->string('certification')->nullable();
            $table->text('faqs')->nullable();
            $table->string('date');
            $table->string('time');
            $table->text('facilitators');
            $table->string('cover');
            $table->timestamps();

            $table->foreignId('organization_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
