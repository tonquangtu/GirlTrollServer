<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Member;
class MemberTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker::create();
		for($i=0;$i<10; $i++){
			$member = Member::create([
				'member_id'=>$faker->text,
				'username'=>$faker->username,
				'rank'=>$faker->randomNumber(null),
				'like'=>$faker->randomNumber(null),
				'avatar_url'=>$faker->text,
				'total_image'=>$faker->randomNumber(null)
			]);
		}
	}

}
