<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInterviewPendingCandidateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('interview_pending_candidate', function(Blueprint $table)
		{
			$table->integer('job_id');
			$table->integer('candidate_id');
			$table->integer('ins_job_candidate_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('interview_pending_candidate');
	}

}
