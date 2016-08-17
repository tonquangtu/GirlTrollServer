<?php

use Illuminate\Database\Seeder;
use App\Feed;
use Faker\Factory as Faker;
class FeedTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker::create();
		for($i=0;$i<5; $i++){
			$feed = Feed::create([
				'title'=>$faker->text,
				'school'=>$faker->text,
				'time'=>$faker->datetime,
				'like'=>$faker->randomNumber(null),
				'comment'=>$faker->randomNumber(null),
				'share'=>$faker->randomNumber(null),
				'member_id'=>'1'
			]);
		}
		for($i=0;$i<5; $i++){
			$feed = Feed::create([
				'title'=>$faker->text,
				'school'=>$faker->text,
				'time'=>$faker->datetime,
				'like'=>$faker->randomNumber(null),
				'comment'=>$faker->randomNumber(null),
				'share'=>$faker->randomNumber(null),
				'member_id'=>'2'
			]);
		}
		for($i=0;$i<5; $i++){
			$feed = Feed::create([
				'title'=>$faker->text,
				'school'=>$faker->text,
				'time'=>$faker->datetime,
				'like'=>$faker->randomNumber(null),
				'comment'=>$faker->randomNumber(null),
				'share'=>$faker->randomNumber(null),
				'member_id'=>'3'
			]);
		}
		for($i=0;$i<5; $i++){
			$feed = Feed::create([
				'title'=>$faker->text,
				'school'=>$faker->text,
				'time'=>$faker->datetime,
				'like'=>$faker->randomNumber(null),
				'comment'=>$faker->randomNumber(null),
				'share'=>$faker->randomNumber(null),
				'member_id'=>'4'
			]);
		}
	}

}
