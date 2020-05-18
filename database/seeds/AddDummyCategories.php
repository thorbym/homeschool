<?php

use Illuminate\Database\Seeder;

class AddDummyCategories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
			[
				'category' => 'Maths',
				'colour' => '#ffa951'
			],
			[
				'category' => 'English',
				'colour' => '#eae22b'
			],
			[
				'category' => 'ICT',
				'colour' => '#70dfa4'
			],
			[
				'category' => 'Music',
				'colour' => '#20c3a6'
			],
        ];
        // #f05373
        \DB::table('categories')->insert($categories);
    }
}