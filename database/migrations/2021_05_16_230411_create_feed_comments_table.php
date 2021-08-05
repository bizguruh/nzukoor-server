<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment')->nullable();
            $table->foreignId('feed_id')->nullable()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('facilitator_id')->nullable()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('admin_id')->nullable()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('organization_id')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('feed_comments');
    }
}
