<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacilitatorRoleToFacilitatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facilitators', function (Blueprint $table) {
            $table->string('facilitator_role')->nullable();
            $table->string('bank_name')->nullable();
            $table->bigInteger('account_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facilitators', function (Blueprint $table) {
            $table->dropColumn('facilitator_role', 'bank_name', 'account_number');
        });
    }
}
