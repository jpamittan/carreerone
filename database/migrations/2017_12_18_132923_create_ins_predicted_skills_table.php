<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsPredictedSkillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_predicted_skills', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id');
			$table->integer('skill_id');
			$table->integer('resume_id');
			$table->float('percentage', 10, 4);
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
		Schema::drop('ins_predicted_skills');
	}

}
