<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsInterviewsCalandarTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_interviews_calandar', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->integer('candidate_id');
			$table->date('interview_date');
			$table->string('interview_time', 200);
			$table->string('interview_minutes', 20);
			$table->string('comment', 2000)->nullable();
			$table->text('feedback', 65535);
			$table->boolean('status')->nullable();
			$table->integer('interview_status');
			$table->timestamps();
			$table->softDeletes();
			$table->smallInteger('email_reminder')->nullable()->default(0);
			$table->string('reminder_sent', 250)->nullable();
			$table->string('feedback_sent', 45)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_interviews_calandar');
	}

}
