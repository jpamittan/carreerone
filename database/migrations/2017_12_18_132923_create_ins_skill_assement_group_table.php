<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSkillAssementGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_skill_assement_group', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('skill_group_name', 2000);
			$table->integer('crm_group_id')->nullable();
			$table->integer('is_active')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_skill_assement_group');
	}

}
