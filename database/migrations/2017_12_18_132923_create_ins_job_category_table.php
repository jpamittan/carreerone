<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_job_category', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('category_name')->nullable();
			$table->timestamps();
			$table->string('ins_job_category_id', 50)->nullable();
			$table->integer('job_category_type_id')->nullable();
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
		Schema::drop('ins_job_category');
	}

}
