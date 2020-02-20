<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('to', 40);
			$table->string('from', 40);
			$table->string('subject', 400);
			$table->text('message');
			$table->string('additional_details', 400);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email');
	}

}
