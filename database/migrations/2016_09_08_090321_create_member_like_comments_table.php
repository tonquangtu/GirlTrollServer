<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLikeCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('member_like_comment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('member_id')->unsigned();
			$table->integer('comment_id')->unsigned();
			$table->tinyInteger('is_like');
			$table->foreign('member_id')->references('id')->on('member')->onDelete('cascade');
			$table->foreign('comment_id')->references('id')->on('comment')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('member_like_comment');
	}

}
