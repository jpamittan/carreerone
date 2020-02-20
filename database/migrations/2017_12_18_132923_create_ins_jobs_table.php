<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_jobs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('job_title')->nullable();
			$table->integer('agency_branch_id')->nullable();
			$table->string('vacancy_reference_id', 200)->nullable();
			$table->date('appreoved_date')->nullable();
			$table->string('job_function', 100)->nullable();
			$table->integer('job_category_id')->nullable();
			$table->string('suburb')->nullable();
			$table->string('state', 200)->nullable();
			$table->string('postcode', 200)->nullable();
			$table->string('salary_package', 20)->nullable();
			$table->integer('salary_from')->nullable();
			$table->integer('salary_to')->nullable();
			$table->string('job_grade', 150)->nullable();
			$table->integer('employment_status_id')->nullable();
			$table->text('position_description', 65535);
			$table->text('role_description', 65535)->nullable();
			$table->string('selection_criteria')->nullable();
			$table->string('enquirey_name', 200)->nullable();
			$table->string('enquire_number', 200)->nullable();
			$table->string('prepared_by_name', 200)->nullable();
			$table->string('prepared_by_number', 150)->nullable();
			$table->string('prepared_by_email')->nullable();
			$table->date('deadline_date')->nullable();
			$table->dateTime('is_expired')->nullable();
			$table->string('cluster_only', 200)->nullable();
			$table->string('agency_only', 200)->nullable();
			$table->string('job_type', 200)->nullable();
			$table->timestamps();
			$table->date('deleted_at')->nullable();
			$table->string('jobid', 50)->nullable();
			$table->string('location', 50)->nullable();
			$table->integer('agency_id')->nullable()->default(0);
			$table->integer('job_category_type_id')->nullable()->default(0);
			$table->string('jobstatus', 50)->nullable();
			$table->text('advert', 65535)->nullable();
			$table->string('hiring_manager_name', 250)->nullable();
			$table->string('hiring_manager_email', 250)->nullable();
			$table->string('hiring_manager_phone', 250)->nullable();
			$table->string('workplace_location', 250)->nullable();
			$table->string('length_term', 250)->nullable();
			$table->string('length_term_other', 2000)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_jobs');
	}

}
