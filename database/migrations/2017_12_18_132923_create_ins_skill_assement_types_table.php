<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSkillAssementTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_skill_assement_types', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_category_id');
			$table->string('skil_names', 2000);
			$table->string('ins_skill_id', 100)->nullable();
			$table->integer('is_active')->nullable()->default(1);
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
		Schema::drop('ins_skill_assement_types');
	}

}
