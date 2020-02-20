<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSkillmatchJobTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_skillmatch_job', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->integer('skill_id');
			$table->timestamps();
			$table->float('distance', 10, 4)->nullable();
			$table->integer('status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_skillmatch_job');
	}

}
