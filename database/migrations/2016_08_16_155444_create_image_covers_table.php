<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageCoversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('image_cover', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('title');
			$table->text('url_image');
			$table->text('url_image_thumbnail');
			$table->bigInteger('number_cover')->default(0);
			// $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('image_cover');
	}

}
