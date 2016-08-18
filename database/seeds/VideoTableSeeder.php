<?php

use Illuminate\Database\Seeder;
use App\Video;
use Faker\Factory as Faker;
class VideoTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//['id','url_video','type','feed_id']
		$faker = Faker::create();
		for($j=1; $j<=20; $j++){
			for($i=0;$i<rand(1,5); $i++){
				$feed = Video::create([
					'url_video'=>$faker->text,
					'type'=>'1',
					'feed_id'=>$j
				]);
			}
		}
		
	}

}
