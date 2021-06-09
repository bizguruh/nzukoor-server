<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacilitatorIdToCourseCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_communities', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('facilitator_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_communities', function (Blueprint $table) {
            $table->dropColumn('admin_id', 'facilitator_id');
        });
    }
}
