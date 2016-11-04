<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('image_event', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('image_id')->unsigned();
			$table->integer('event_id')->unsigned();
			$table->foreign('image_id')->references('id')->on('image');
			$table->foreign('event_id')->references('id')->on('event');
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
		Schema::drop('image_event');
	}

}
