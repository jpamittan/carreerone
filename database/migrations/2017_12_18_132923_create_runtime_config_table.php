<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRuntimeConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('runtime_config', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->string('value', 2500);
			$table->string('description')->nullable();
			$table->string('is_boolean')->default('0');
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
		Schema::drop('runtime_config');
	}

}
