<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSkillCandidateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_skill_candidate', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id')->unsigned();
			$table->integer('skill_id');
			$table->integer('resume_id');
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
		Schema::drop('ins_skill_candidate');
	}

}
