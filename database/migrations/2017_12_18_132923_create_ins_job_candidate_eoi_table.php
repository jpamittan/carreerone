<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobCandidateEoiTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_job_candidate_eoi', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->integer('candidate_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();
			$table->string('ins_pushed', 1)->nullable()->default('N');
			$table->string('ins_job_apply_id', 50)->nullable();
			$table->integer('submit_status')->nullable()->default(0);
			$table->string('comments', 250)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_job_candidate_eoi');
	}

}
