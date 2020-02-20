<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCapabilityJobTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_capability_job', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->integer('capability_name_id');
			$table->integer('level_id');
			$table->integer('group_id');
			$table->boolean('core_status');
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
		Schema::drop('ins_capability_job');
	}

}
