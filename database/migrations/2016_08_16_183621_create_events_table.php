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
		Schema::create('event', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('title');
			$table->text('url_image_event');
			$table->text('short_content');
			$table->text('content');
			$table->smallInteger('type')->length(6);
			$table->text('policy');
			$table->tinyInteger('active');
			$table->datetime('time_start');
			$table->datetime('time_end');
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
		Schema::drop('event');
	}

}
