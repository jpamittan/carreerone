<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsEmployeesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_employees', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('createdby', 50)->nullable();
			$table->timestamps();
			$table->string('ins_jobpreference1', 50)->nullable();
			$table->string('ins_jobpreference2', 50)->nullable();
			$table->string('ins_jobpreference3', 50)->nullable();
			$table->string('ins_linkedinurl', 100)->nullable();
			$table->string('ins_skypeid', 50)->nullable();
			$table->string('modifiedby', 50)->nullable();
			$table->string('new_activejobseekingexternal', 1)->nullable()->default('N');
			$table->integer('new_age')->nullable();
			$table->boolean('new_atsi')->nullable();
			$table->string('new_careerplancompleted', 1)->nullable()->default('N');
			$table->dateTime('new_completedprogram')->nullable();
			$table->dateTime('new_dateofbirth')->nullable();
			$table->integer('new_daysundermanagement')->nullable();
			$table->integer('new_daysuntilfrexit')->nullable();
			$table->dateTime('new_decisiondate')->nullable();
			$table->integer('new_defaultjobmatchingjobcategorytype')->nullable();
			$table->string('new_eitid', 50)->nullable();
			$table->string('new_emergencyaddressline1', 100)->nullable();
			$table->string('new_emergencyaddressline2', 100)->nullable();
			$table->string('new_emergencycontactname', 100)->nullable();
			$table->string('new_emergencycontactnumber', 20)->nullable();
			$table->string('new_emergencyemail', 100)->nullable();
			$table->string('new_emergencymobilenumber', 20)->nullable();
			$table->string('new_emergencypostcode', 10)->nullable();
			$table->string('new_emergencyrelationship', 50)->nullable();
			$table->string('new_emergencystate', 10)->nullable();
			$table->string('new_emergencysuburbid', 50)->nullable();
			$table->string('new_employeenumber', 50)->nullable();
			$table->string('new_employeestatus', 20)->nullable();
			$table->string('new_employmentrestrictions', 100)->nullable();
			$table->dateTime('new_excessdate')->nullable();
			$table->string('new_exitdocumentscompleted', 100)->nullable();
			$table->dateTime('new_exitmeetingdate')->nullable();
			$table->string('new_exittype', 50)->nullable();
			$table->dateTime('new_finalexitdate')->nullable();
			$table->string('new_financialplanningsession', 1)->nullable()->default('N');
			$table->string('new_firstname', 50)->nullable();
			$table->dateTime('new_forcedredundancyexitdate')->nullable();
			$table->dateTime('new_forcedredundancyexitmeetingdate')->nullable();
			$table->dateTime('new_forcedredundancyretentionstartdate')->nullable();
			$table->string('new_gender', 20)->nullable();
			$table->string('new_hrcontact', 50)->nullable();
			$table->dateTime('new_hrvrrdinfosession')->nullable();
			$table->string('new_inductionattended', 1)->nullable()->default('N');
			$table->string('new_inductionattendedreason', 100)->nullable();
			$table->dateTime('new_inductiondate')->nullable();
			$table->dateTime('new_inductiondeferreddate')->nullable();
			$table->string('new_intentiontoforceretrenchletterissued', 1)->nullable()->default('N');
			$table->string('new_jobclublevelfriday', 50)->nullable();
			$table->string('new_jobclublevelmonday', 50)->nullable();
			$table->string('new_jobclublevelthursday', 50)->nullable();
			$table->string('new_jobclubleveltuesday', 50)->nullable();
			$table->string('new_jobclublevelwednesday', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype1', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype2', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype3', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype4', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype5', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype6', 50)->nullable();
			$table->string('new_jobmatchingjobcategorytype7', 50)->nullable();
			$table->integer('new_jobmatchingjobgrade')->nullable();
			$table->text('new_jobmatchingkeyskills', 65535)->nullable();
			$table->integer('new_jobmatchinglocation1')->nullable();
			$table->integer('new_jobmatchinglocation2')->nullable();
			$table->integer('new_jobmatchinglocation3')->nullable();
			$table->integer('new_jobmatchinglocation4')->nullable();
			$table->text('new_jobmatchingqualifications', 65535)->nullable();
			$table->decimal('new_jobmatchingsalaryfrom', 10, 0)->nullable();
			$table->decimal('new_jobmatchingsalaryto', 10, 0)->nullable();
			$table->string('new_matchallcat', 1)->nullable()->default('N');
			$table->string('new_meepaffected', 1)->nullable()->default('N');
			$table->string('new_payrollcontact', 50)->nullable();
			$table->string('new_payrollgroup', 50)->nullable();
			$table->string('new_personalcontactnumber', 20)->nullable();
			$table->string('new_personalemail', 50)->nullable();
			$table->string('new_personalhomenumber', 20)->nullable();
			$table->string('new_personalmobilenumber', 20)->nullable();
			$table->string('new_previousattendee', 1)->nullable()->default('N');
			$table->string('new_programtype', 50)->nullable();
			$table->dateTime('new_pswregistration')->nullable();
			$table->string('new_pvpostvrplans', 50)->nullable();
			$table->string('new_pwaddressline1', 100)->nullable();
			$table->string('new_pwaddressline2', 100)->nullable();
			$table->string('new_pwagency', 50)->nullable();
			$table->string('new_pwagencybranch', 50)->nullable();
			$table->string('new_pwcontactnumber', 20)->nullable();
			$table->string('new_pwemail', 50)->nullable();
			$table->string('new_pwemploymentstatus', 50)->nullable();
			$table->string('new_pwestimatesattached', 1)->nullable()->default('N');
			$table->string('new_pwexcessletterattached', 1)->nullable()->default('N');
			$table->decimal('new_pwgrademaximumsalary', 10, 0)->nullable();
			$table->decimal('new_pwgrademinimumsalary', 10, 0)->nullable();
			$table->string('new_pwleavebalanceattached', 1)->nullable()->default('N');
			$table->string('new_pwmedicalissues', 1)->nullable()->default('N');
			$table->string('new_pwparttimehours', 20)->nullable();
			$table->string('new_pwperformanceissues', 1)->nullable()->default('N');
			$table->integer('new_pwpositiongrade')->nullable();
			$table->integer('new_pwpositiontitle')->nullable();
			$table->string('new_pwpostcode', 10)->nullable();
			$table->string('new_pwpreviouspositiondescriptionattached', 1)->nullable()->default('N');
			$table->string('new_pwpreviouspositionhistoryattached', 1)->nullable()->default('N');
			$table->string('new_pwrosterrequirements', 100)->nullable();
			$table->decimal('new_pwsalary', 10, 0)->nullable();
			$table->dateTime('new_pwservicestartdate')->nullable();
			$table->string('new_pwstate', 10)->nullable();
			$table->string('new_pwsuburb', 50)->nullable();
			$table->string('new_pwtrainingrecordattached', 1)->nullable()->default('N');
			$table->string('new_redeployedagencyid', 50)->nullable();
			$table->string('new_redeployedposition', 100)->nullable();
			$table->decimal('new_redeployedsalary', 10, 0)->nullable();
			$table->dateTime('new_redeployedstartdate')->nullable();
			$table->string('new_redeployedsupervisorid', 50)->nullable();
			$table->string('new_redeployeepdactivated', 1)->nullable()->default('N');
			$table->string('new_redeploymentpolicy', 200)->nullable();
			$table->string('new_redeploymentsource', 100)->nullable();
			$table->dateTime('new_referraldate')->nullable();
			$table->string('new_registeredfortransitmatchingprogram', 1)->nullable()->default('N');
			$table->dateTime('new_registeredtransitmatchingprogramdate')->nullable();
			$table->string('new_residentialaddressline1', 100)->nullable();
			$table->string('new_residentialaddressline2', 100)->nullable();
			$table->string('new_residentialpostcode', 10)->nullable();
			$table->string('new_residentialstate', 50)->nullable();
			$table->integer('new_residentialsuburbid')->nullable();
			$table->string('new_resumecompleted', 1)->nullable()->default('N');
			$table->dateTime('new_retentionenddate')->nullable();
			$table->string('new_skillsauditcomplete', 1)->nullable()->default('N');
			$table->dateTime('new_skillsauditcompletedate')->nullable();
			$table->string('new_surname', 50)->nullable();
			$table->string('new_title', 100)->nullable();
			$table->string('new_transitionactivity', 100)->nullable();
			$table->string('new_transitionmanagerallocated', 1)->nullable()->default('N');
			$table->dateTime('new_vreoidate')->nullable();
			$table->dateTime('new_vrexitdate')->nullable();
			$table->dateTime('new_vrofferexcessdate')->nullable();
			$table->string('new_vrofferextensionprovided', 1)->nullable()->default('N');
			$table->string('new_vrredeploymentchoice', 100)->nullable();
			$table->string('new_willingtorelocate', 1)->nullable()->default('N');
			$table->text('new_willingtorelocatedetails', 65535)->nullable();
			$table->integer('new_yearsofservice')->nullable();
			$table->string('ownerid', 100)->nullable();
			$table->string('statecode', 10)->nullable();
			$table->integer('transactioncurrencyid')->nullable();
			$table->string('employeeid', 50)->nullable();
			$table->integer('user_id')->nullable();
			$table->boolean('ins_culturallyandlinguisticallydiverse')->nullable();
			$table->boolean('ins_disability')->nullable();
			$table->text('ins_reasonableadjustmentrequired', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_employees');
	}

}
