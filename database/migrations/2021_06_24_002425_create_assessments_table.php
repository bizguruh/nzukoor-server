<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('start');
            $table->string('end');
            $table->string('feedback');
            $table->string('type');
            $table->string('duration');
            $table->text('tools');
            $table->string('status');
            $table->foreignId('question_template_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('facilitator_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('assessments');
    }
}
