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
			$table->increments('feeId')->length(11);
			$table->text('title');
			$table->string('school')->length(255);
			$table->datetime('time');
			$table->bigInteger('like')->length(20);
			$table->bigInteger('comment')->length(20);
			$table->bigInteger('share')->length(20);
			$table->string('memberId')->length(255);
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
		Schema::drop('feed');
	}

}
