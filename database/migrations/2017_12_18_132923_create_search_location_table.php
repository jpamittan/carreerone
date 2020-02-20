<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSearchLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('search_location', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('parent_id')->unsigned()->nullable()->index('parent_id');
			$table->string('name');
			$table->string('url', 4000)->nullable()->index('url');
			$table->integer('order')->unsigned()->default(1);
			$table->string('seourl')->nullable();
			$table->string('google_location')->nullable();
			$table->string('lid')->nullable();
			$table->string('indeed_location')->nullable();
			$table->string('indeed_radius', 45)->nullable();
			$table->string('indeed_country', 45)->nullable();
			$table->string('filter_lid')->nullable();
			$table->string('filter_state')->nullable();
			$table->integer('percentage')->nullable();
			$table->integer('jobs_per_call')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('search_location');
	}

}
