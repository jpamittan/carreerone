<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobCategoryTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_job_category_types', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('category_type_name')->nullable();
			$table->timestamps();
			$table->string('ins_job_category_type_id', 45)->nullable();
			$table->smallInteger('is_active')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_job_category_types');
	}

}
