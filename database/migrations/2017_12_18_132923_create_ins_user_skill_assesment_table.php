<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsUserSkillAssesmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_user_skill_assesment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('candidate_id');
			$table->integer('skill_asse_type_id');
			$table->timestamps();
			$table->integer('recency_id')->nullable();
			$table->integer('frequency_id')->nullable();
			$table->integer('level_id')->nullable();
			$table->text('comment', 65535)->nullable();
			$table->string('ins_skillsummary_id', 100)->nullable();
			$table->boolean('active')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_user_skill_assesment');
	}

}
