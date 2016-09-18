<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('member', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('facebook_id')->length(100)->nullable();
			$table->string('username')->length(255);
			$table->integer('gmail')->length(11)->nullable();
			$table->string('password')->nullable();
			$table->bigInteger('like')->length(16);
			$table->string('avatar_url')->length(255);
			$table->integer('total_image')->length(11);
			// $table->timestamps();
			// $table->primary('memberId');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('member');
	}

}
