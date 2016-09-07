<?php

use Illuminate\Database\Seeder;
use App\Event;
use Faker\Factory as Faker;
class EventTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker::create();
		for($i=0;$i<5; $i++){
			// ['id','title','short_content','content','type','policy','active','time_start','time_end']
			$feed = Event::create([
				'title'=>$faker->text,
				'short_content'=>$faker->text,
				'content'=>$faker->text,
				'type'=>1,
				'policy'=>$faker->text,
				'active'=>1,
				'time_start'=>$faker->datetime,
				'time_end'=>$faker->datetime
			]);
		}
	}

}
