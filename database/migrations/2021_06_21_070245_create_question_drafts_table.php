<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_drafts', function (Blueprint $table) {
            $table->id();
            $table->longText('sections');
            $table->string('interest')->nullable();
            $table->string('title');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('facilitator_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('question_drafts');
    }
}
