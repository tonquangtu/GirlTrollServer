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
			$table->increments('imageId')->length(11);
			$table->text('urlImage');
			$table->integer('type')->length(11);
			$table->string('linkFace')->length(255);
			$table->integer('feedId')->length(11);
			$table->text('urlImageThumbnail');
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
		Schema::drop('image');
	}

}
