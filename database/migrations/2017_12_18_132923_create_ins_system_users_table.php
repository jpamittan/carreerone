<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsSystemUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_system_users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('systemuserid', 200);
			$table->string('fullname', 200)->nullable();
			$table->string('internalemailaddress', 200);
			$table->dateTime('createdon')->nullable();
			$table->string('organizationid', 200);
			$table->timestamps();
			$table->string('phone', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ins_system_users');
	}

}
