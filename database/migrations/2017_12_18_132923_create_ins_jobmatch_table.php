<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobmatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_jobmatch', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->string('new_jobmatchedid', 2000);
			$table->integer('candidate_id');
			$table->integer('location_status');
			$table->integer('category_status');
			$table->timestamps();
			$table->date('email_date')->nullable();
			$table->integer('email_limit')->nullable()->default(0);
			$table->integer('new_matchstatus')->nullable();
			$table->string('ins_pushed', 5)->nullable()->default('N');
			$table->string('comments', 5000)->nullable();
			$table->string('match_status')->nullable();
			$table->string('salary_status', 50)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_jobmatch');
	}

}
