<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsAgencyDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_agency_details', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('agency_name');
			$table->string('division')->nullable();
			$table->string('grade_band', 200)->nullable();
			$table->string('kind_employment', 200)->nullable();
			$table->string('anzsco_code', 200)->nullable();
			$table->string('role_number', 200)->nullable();
			$table->string('pcat_code', 200)->nullable();
			$table->dateTime('approval_date')->nullable();
			$table->string('agency_website')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->string('ins_agency_id', 50)->nullable();
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
		Schema::drop('ins_agency_details');
	}

}
