<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('eventId')->length(11);
			$table->text('title');
			$table->text('sortContent');
			$table->text('content');
			$table->smallInteger('type')->length(6);
			$table->text('policy');
			$table->tinyInteger('active');
			$table->datetime('timeStart');
			$table->datetime('timeEnd');
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
		Schema::drop('events');
	}

}
