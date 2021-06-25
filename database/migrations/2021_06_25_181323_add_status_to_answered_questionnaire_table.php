<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAnsweredQuestionnaireTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answered_questionnaire', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->string('total_score')->nullable();
            $table->string('your_score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('answered_questionnaire', function (Blueprint $table) {
            $table->dropColumn('status', 'your_score', 'total_score');
        });
    }
}
