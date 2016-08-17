<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_event', function(Blueprint $table)
		{	
			$table->increments('id');
			$table->integer('event_id')->unsigned();
			$table->integer('member_id')->unsigned();
			$table->integer('image_id')->unsigned();
			// $table->timestamps();
			$table->foreign('event_id')->references('id')->on('event');
			$table->foreign('member_id')->references('id')->on('member');
			$table->foreign('image_id')->references('id')->on('image');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_event');
	}

}
