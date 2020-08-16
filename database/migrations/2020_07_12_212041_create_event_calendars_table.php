<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // THIS is a one-off script to transfer the recurring events to individual events
    public function up()
    {
        // do a backup anyway
        DB::statement('CREATE TABLE events_backup LIKE events');
        DB::statement('INSERT INTO events_backup SELECT * FROM events');

        Schema::table('events', function (Blueprint $table) {
            $table->string('timezone', 64)->nullable();
        });

        // this is a migration, all the events are "Europe/London" timezone
        $timezone = "Europe/London";

        // create event_calendars table
        Schema::create('event_calendars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('event_id');
            $table->dateTime('start')->nullable();
            $table->dateTime('start_utc')->nullable();
            $table->dateTime('end')->nullable();
            $table->dateTime('end_utc')->nullable();
            $table->string('utc_offset', 6)->nullable();
        });

        // get all the timed events at this moment in time
        $events = DB::table('events')
            ->whereNotNull('start_time')
            ->get();

        // loop through each event
        foreach ($events as $event) {
            DB::table('events')
                ->where('id', $event->id)
                ->update([
                    'timezone' => $timezone
                ]);
            // make an arbitrary start date of 1st March 2020
            $date = "2020-04-01";
            // turn the event's days_of_week string into an array
            $daysOfWeek = json_decode($event->days_of_week);
            // loop around 600 "days"
            for ($i = 0; $i < 600; $i++) {
                // get the "day of week" number of this particular date
                $dayNumber = date('N', strtotime($date));
                // php has sunday as 7, but TeachEm calendar recognises Sunday as 0
                $dayNumber = $dayNumber == 7 ? 0 : $dayNumber;
                // if this event has an occurence on this day number, then insert a row
                if (in_array($dayNumber, $daysOfWeek)) {

                    // calc utc offset by making a UTC DateTime using the datetime in question, and comparing it with the timezone that's passed in (in this case, will always be London)
                    $timezoneCheck = new DateTime($date . ' ' . $event->start_time, new DateTimeZone($timezone));
                    $offset = $timezoneCheck->getOffset();

                    // $utc_offset needs to be Z for zero, or convert the seconds (eg. 01:00)
                    if ($offset === 0) {
                        $utc_offset = "Z";
                    } else if ($offset > 0) {
                        $utc_offset = "+" . gmdate("H:i", $offset);
                    } else {
                        $offset = -$offset;
                        $utc_offset = "-" . gmdate("H:i", $offset);
                    }

                    $saveArr = [
                        'event_id' => $event->id,
                        'start' => $date . ' ' . $event->start_time,
                        'start_utc' => gmdate('Y-m-d H:i', strtotime($date . 'T' . $event->start_time . $utc_offset)),
                        'end' => $date . ' ' . $event->end_time,
                        'end_utc' => gmdate('Y-m-d H:i', strtotime($date . 'T' . $event->end_time . $utc_offset)),
                        'utc_offset' => $utc_offset
                    ];

                    DB::table('event_calendars')
                        ->insert($saveArr);
                }
                // increment the date by a day, and run the whole thing again
                $date = date('Y-m-d', strtotime($date . ' +1 day'));
                if ($date >= "2022-12-31") {
                    break;
                }
            }
        }
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
