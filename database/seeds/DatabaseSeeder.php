<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create users
        factory(App\User::class, 10)->create();

        // create categories
        factory(App\Category::class, 10)->create();

        // create events
        factory(App\Event::class, 100)->create()->each(function($event) {
            if ($event->start_time) {
                // attach an eventCalendar to the event, if it's a live event
                $this->attachEventCalendarToEvent($event);
            }
        });
    }

    private function attachEventCalendarToEvent($event)
    {
        // start from today
        $date = date('Y-m-d');

        for ($i = 0; $i < 600; $i++) {

            // get the "day of week" number of this particular date
            $dayNumber = date('N', strtotime($date));
            // php has sunday as 7, but TeachEm calendar recognises Sunday as 0
            $dayNumber = $dayNumber == 7 ? 0 : $dayNumber;

            // if this event has an occurence on this day number, then insert a row
            if (in_array($dayNumber, json_decode($event->days_of_week))) {

                // calc utc offset by making a UTC DateTime using the datetime in question, and comparing it with the timezone that's passed in (in this case, will always be London)
                $timezoneCheck = new \DateTime($date . ' ' . $event->start_time, new \DateTimeZone($event->timezone));
                $offset = $timezoneCheck->getOffset();

                // $utc_offset needs to be Z for zero, or convert the seconds (eg. 01:00)
                if ($offset === 0) {
                    $utcOffset = "Z";
                } else if ($offset > 0) {
                    $utcOffset = "+" . gmdate("H:i", $offset);
                } else {
                    $offset = -$offset;
                    $utcOffset = "-" . gmdate("H:i", $offset);
                }

                $saveArr = [
                    'event_id' => $event->id,
                    'start' => $date . ' ' . $event->start_time,
                    'start_utc' => gmdate('Y-m-d H:i', strtotime($date . 'T' . $event->start_time . $utcOffset)),
                    'end' => $date . ' ' . $event->end_time,
                    'end_utc' => gmdate('Y-m-d H:i', strtotime($date . 'T' . $event->end_time . $utcOffset)),
                    'utc_offset' => $utcOffset
                ];

                DB::table('event_calendars')
                    ->insert($saveArr);

            }

            // increment the date by a day, and run the whole thing again
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        }
    }
}
