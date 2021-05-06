<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inboxes', function (Blueprint $table) {
            $table->id();
            $table->text('message')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('facilitator_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
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
        Schema::dropIfExists('inboxes');
    }
}
