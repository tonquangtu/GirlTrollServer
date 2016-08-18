<?php

use Illuminate\Database\Seeder;
use App\ImageCover;
use Faker\Factory as Faker;
class ImageCoverTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker::create();
		for($i=0;$i<30; $i++){
			$feed = ImageCover::create([
				'title' =>$faker->text,
				'url_image'=>$faker->text,
				'url_image_thumbnail'=>$faker->text,
				'number_cover'=>$faker->randomNumber(null)
			]);
		}
	}

}
