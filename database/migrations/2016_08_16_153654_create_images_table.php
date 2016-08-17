<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('image', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('url_image');
			$table->integer('type')->length(11);
			$table->string('link_face')->length(255);
			$table->text('url_image_thumbnail');
			$table->integer('feed_id')->unsigned();
			// $table->timestamps();
			$table->foreign('feed_id')->references('id')->on('feed');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('image');
	}

}
