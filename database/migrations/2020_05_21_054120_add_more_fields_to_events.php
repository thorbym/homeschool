<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('requires_supervision')->default(0);
            $table->boolean('dfe_approved')->default(0);
            $table->string('catchup_link');
            $table->integer('minimum_age');
            $table->integer('maximum_age');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('requires_supervision');
            $table->dropColumn('dfe_approved');
            $table->dropColumn('catchup_link');
            $table->dropColumn('minimum_age');
            $table->dropColumn('maximum_age');
        });
    }
}
