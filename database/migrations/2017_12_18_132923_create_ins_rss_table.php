<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsRssTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_rss', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title', 2000);
			$table->string('link', 200);
			$table->dateTime('published');
			$table->string('guid', 200);
			$table->string('thumbnail', 200)->nullable();
			$table->string('description', 200)->nullable();
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
		Schema::drop('ins_rss');
	}

}
