<?php

use Illuminate\Database\Seeder;
use App\Image;
use Faker\Factory as Faker;
class ImageTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker::create();
		for($i=0;$i<3; $i++){
			$feed = Image::create([
				'url_image'=>$faker->text,
				'type'=>'1',
				'link_face'=>$faker->text,
				'url_image_thumbnail'=>$faker->text,
				'feed_id'=>'17'
			]);
		}
		for($i=0;$i<4; $i++){
			$feed = Image::create([
				'url_image'=>$faker->text,
				'type'=>'0',
				'link_face'=>$faker->text,
				'url_image_thumbnail'=>$faker->text,
				'feed_id'=>'18'
			]);
		}
		for($i=0;$i<6; $i++){
			$feed = Image::create([
				'url_image'=>$faker->text,
				'type'=>'1',
				'link_face'=>$faker->text,
				'url_image_thumbnail'=>$faker->text,
				'feed_id'=>'19'
			]);
		}
		for($i=0;$i<2; $i++){
			$feed = Image::create([
				'url_image'=>$faker->text,
				'type'=>'1',
				'link_face'=>$faker->text,
				'url_image_thumbnail'=>$faker->text,
				'feed_id'=>'20'
			]);
		}
	}

}
