<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsInterviewPendingDatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_interview_pending_dates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->integer('ins_job_candidate_id')->nullable();
			$table->string('interviewer_name')->nullable();
			$table->string('interviewer_title')->nullable();
			$table->date('interview_dates');
			$table->string('interview_timings', 200);
			$table->integer('time');
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
		Schema::drop('ins_interview_pending_dates');
	}

}
