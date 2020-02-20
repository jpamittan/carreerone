<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCalandarTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_calandar', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('interviewer', 200);
			$table->string('candidate', 200);
			$table->integer('interview_date');
			$table->integer('job_id');
			$table->boolean('confirm_interview');
			$table->dateTime('created_at');
			$table->dateTime('udated_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_calandar');
	}

}
