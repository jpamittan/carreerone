<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCapabilityMatchNamesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_capability_match_names', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('match_names', 200);
			$table->string('crm_user_names', 200);
			$table->string('crm_match_names');
			$table->string('crm_match_core_status', 200)->nullable();
			$table->integer('group_id');
			$table->string('crm_gap_per', 200)->nullable();
			$table->string('crm_user_core_status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_capability_match_names');
	}

}
