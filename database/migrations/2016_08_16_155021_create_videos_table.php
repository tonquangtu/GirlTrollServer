<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('url_video');
			$table->tinyInteger('type');
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
		Schema::drop('video');
	}

}
