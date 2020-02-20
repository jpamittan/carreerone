<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('location', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('city', 100);
			$table->string('state', 100);
			$table->string('country_code', 20);
			$table->string('post_code', 100)->nullable();
			$table->string('map_location_id', 100)->nullable();
			$table->string('combi', 500);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('location');
	}

}
