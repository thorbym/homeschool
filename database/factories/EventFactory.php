<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Event;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Event::class, function (Faker $faker) {
	$start_hour = rand(7, 20);
	$end_hour = $start_hour + rand(1, 2);
	if (strlen($start_hour) == 1) {
		$start_hour = '0' . $start_hour;
	}
	if (strlen($end_hour) == 1) {
		$end_hour = '0' . $end_hour;
	}
	$start_time = $start_hour . ':00';
	$end_time = $end_hour . ':00';
	$days_of_week_array = [
		'["1","2","3","4","5","6","0"]',
		'["1","3","5","0"]',
		'["1","5"]',
		'["4"]'
	];
	$timezone_array = [
		'Europe/London',
		'Europe/Berlin',
		'America/Los_Angeles',
		'America/New_York'
	];
	$live = rand(0, 1);
	$minimum_age = rand(4, 14);
	$maximum_age = rand($minimum_age, 16);
    return [
		'title' => $faker->sentence(rand(3,8), true),
		'category_id' => rand(1,10),
		'description' => $faker->paragraph(rand(1,3), true),
		'live_web_link' => $live ? $faker->url : null,
		'start_time' => $live ? $start_time : null,
		'end_time' => $live ? $end_time : null,
		'days_of_week' => $live ? $days_of_week_array[rand(0, 3)] : null,
		'requires_supervision' => rand(0, 1),
		'dfe_approved' => rand(0, 1),
		'web_link' => $faker->url,
		'minimum_age' => $minimum_age,
		'maximum_age' => $maximum_age,
		'live_youtube_link' => $live ? $faker->url : null,
		'live_facebook_link' => $live ? $faker->url : null,
		'live_instagram_link' => $live ? $faker->url : null,
		'youtube_link' => $faker->url,
		'facebook_link' => $faker->url,
		'instagram_link' => $faker->url,
		'free_content' => rand(0, 1),
		'timezone' => $live ? $timezone_array[rand(0, 3)] : null,
    ];
});