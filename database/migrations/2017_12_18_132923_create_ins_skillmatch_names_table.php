<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSkillmatchNamesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_skillmatch_names', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('skill_name');
			$table->integer('count')->nullable();
			$table->timestamps();
			$table->integer('similar_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_skillmatch_names');
	}

}
