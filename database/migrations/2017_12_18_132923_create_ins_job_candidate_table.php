<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobCandidateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_job_candidate', function(Blueprint $table)
		{
			$table->integer('job_id');
			$table->integer('candidate_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();
			$table->string('ins_pushed', 1)->nullable()->default('N');
			$table->string('ins_job_apply_id', 50)->nullable();
			$table->integer('scheduled')->default(0);
			$table->integer('screened')->default(0);
			$table->integer('id', true);
			$table->integer('submit_status')->nullable()->default(0);
			$table->string('comments', 250)->nullable();
			$table->string('panel_member', 5000)->nullable();
			$table->string('ins_progress', 20)->nullable()->default('0');
			$table->string('mobility_sent', 45)->nullable();
			$table->date('application_due')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_job_candidate');
	}

}
