<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feed', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('title');
			$table->string('school')->length(255);
			$table->datetime('time');
			$table->bigInteger('like')->length(20);
			$table->bigInteger('comment')->length(20);
			// $table->bigInteger('share')->length(20);
			$table->bigInteger('vote')->length(20);
			$table->integer('member_id')->unsigned();
			// $table->timestamps();
			$table->foreign('member_id')->references('id')->on('member');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feed');
	}

}
