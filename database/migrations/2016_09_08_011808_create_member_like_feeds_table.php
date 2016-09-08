<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLikeFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('member_like_feed', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('member_id')->unsigned();
			$table->integer('feed_id')->unsigned();
			$table->tinyInteger('is_like');
			$table->foreign('member_id')->references('id')->on('member')->onDelete('cascade');
			$table->foreign('feed_id')->references('id')->on('feed')->onDelete('cascade');
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
		Schema::drop('member_like_feed');
	}

}
