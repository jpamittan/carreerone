<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCapabilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_capability', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id')->index('job_id');
			$table->integer('candidate_id')->unsigned()->index('candidate_id');
			$table->integer('capability_name_id')->index('capability_name_id');
			$table->integer('level_id');
			$table->float('score', 10);
			$table->integer('core_status');
			$table->float('percentage', 10, 5)->nullable()->default(0.00000);
			$table->timestamps();
			$table->index(['job_id','candidate_id'], 'candidate_composite');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_capability');
	}

}
