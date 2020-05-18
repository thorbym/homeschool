<?php

use Illuminate\Database\Seeder;

class AddDummyEvent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = [
			[
				'title' => 'Event 1',
				'start' => '2020-05-15 09:00:00',
				'end' => '2020-05-15 10:00:00'
			],
			[
				'title' => 'Event 2',
				'start' => '2020-05-15 11:30:00',
				'end' => '2020-05-15 12:00:00'
			],
			[
				'title' => 'Event 3',
				'start' => '2020-05-15 14:00:00',
				'end' => '2020-05-15 15:00:00'
			],
			[
				'title' => 'Event 4',
				'start' => '2020-05-15 15:30:00',
				'end' => '2020-05-15 16:00:00'
			],
        ];
        \DB::table('events')->insert($events);
    }
}
