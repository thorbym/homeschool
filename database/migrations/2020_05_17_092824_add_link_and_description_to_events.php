<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkAndDescriptionToEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('description');
            $table->text('link');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->char('start_time', 10);
            $table->char('end_time', 10);
            $table->dropColumn('start');
            $table->dropColumn('end');
            $table->json('days_of_week');
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
            //
        });
    }
}
