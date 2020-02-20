<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCvTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_cv', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id')->unsigned();
			$table->integer('job_id')->nullable();
			$table->string('resume_url', 2000);
			$table->string('resume_name', 200);
			$table->string('extension', 200)->nullable();
			$table->boolean('is_latest');
			$table->integer('status')->nullable();
			$table->timestamps();
			$table->integer('category_id')->nullable();
			$table->integer('is_applied_resume')->nullable()->default(0);
			$table->string('uploaded_to_monster', 45)->nullable()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_cv');
	}

}
