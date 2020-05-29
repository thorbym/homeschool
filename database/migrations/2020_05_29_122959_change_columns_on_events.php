<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsOnEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('live_web_link')->nullable()->change();
            $table->text('live_youtube_link')->nullable()->change();
            $table->text('live_facebook_link')->nullable()->change();
            $table->text('live_instagram_link')->nullable()->change();
            $table->text('web_link')->nullable()->change();
            $table->text('youtube_link')->nullable()->change();
            $table->text('facebook_link')->nullable()->change();
            $table->text('instagram_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
