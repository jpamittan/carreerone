<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsUserWorkhistoryInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_user_workhistory_info', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id');
			$table->string('job_title', 200)->nullable();
			$table->string('company_name', 200)->nullable();
			$table->integer('start_date_year')->nullable();
			$table->integer('end_date_year')->nullable();
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
		Schema::drop('ins_user_workhistory_info');
	}

}
