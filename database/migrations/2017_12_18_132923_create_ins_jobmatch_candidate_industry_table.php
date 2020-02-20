<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobmatchCandidateIndustryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_jobmatch_candidate_industry', function(Blueprint $table)
		{
			$table->integer('candidate_id');
			$table->integer('job_id')->nullable();
			$table->integer('category_id');
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
		Schema::drop('ins_jobmatch_candidate_industry');
	}

}
