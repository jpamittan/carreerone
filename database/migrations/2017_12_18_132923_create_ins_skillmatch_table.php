<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSkillmatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_skillmatch', function(Blueprint $table)
		{
			$table->integer('candidate_id')->unsigned();
			$table->integer('skill_id');
			$table->boolean('status');
			$table->timestamps();
			$table->integer('job_id');
			$table->integer('id', true);
			$table->primary(['id','candidate_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_skillmatch');
	}

}
