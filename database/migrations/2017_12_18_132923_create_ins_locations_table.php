<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_locations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('location', 100)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->string('ins_location_id', 50)->nullable();
			$table->smallInteger('is_active')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_locations');
	}

}
