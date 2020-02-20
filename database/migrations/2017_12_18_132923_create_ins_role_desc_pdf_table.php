<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsRoleDescPdfTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_role_desc_pdf', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->string('url', 2000);
			$table->string('name', 200);
			$table->boolean('is_latest');
			$table->string('extension', 20);
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
		Schema::drop('ins_role_desc_pdf');
	}

}
