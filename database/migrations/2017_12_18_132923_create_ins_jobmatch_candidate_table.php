<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobmatchCandidateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_jobmatch_candidate', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id');
			$table->integer('job_id')->nullable();
			$table->string('suburb')->nullable();
			$table->string('state', 20)->nullable();
			$table->string('postcode', 200)->nullable();
			$table->integer('salary_from');
			$table->integer('salary_to');
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
		Schema::drop('ins_jobmatch_candidate');
	}

}
