<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCapabilityCandidateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_capability_candidate', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id')->unsigned();
			$table->integer('capability_name_id');
			$table->integer('level_id');
			$table->integer('criteria')->nullable()->default(0);
			$table->integer('core')->nullable()->default(0);
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
		Schema::drop('ins_capability_candidate');
	}

}
