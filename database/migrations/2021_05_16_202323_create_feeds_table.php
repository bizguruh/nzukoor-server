<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->text('media')->nullable();
            $table->text('message')->nullable();
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
        Schema::dropIfExists('feeds');
    }
}
