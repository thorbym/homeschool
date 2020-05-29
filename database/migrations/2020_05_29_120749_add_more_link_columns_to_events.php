<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreLinkColumnsToEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('link', 'live_web_link');//->default(0)->change()
            $table->text('live_youtube_link');
            $table->text('live_facebook_link');
            $table->text('live_instagram_link');
            $table->renameColumn('catchup_link', 'web_link');//->default(0)->change()
            $table->text('youtube_link');
            $table->text('facebook_link');
            $table->text('instagram_link');
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
