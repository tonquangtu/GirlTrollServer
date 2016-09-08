<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('member_id')->unsigned();
			$table->integer('feed_id')->unsigned();
			$table->text('comment');
			$table->bigInteger('num_like');
			$table->datetime('time');
			$table->foreign('member_id')->references('id')->on('member')->onDelete('cascade');
			$table->foreign('feed_id')->references('id')->on('feed')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comment');
	}

}
