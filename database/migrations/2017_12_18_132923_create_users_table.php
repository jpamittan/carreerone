<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('first_name');
			$table->string('last_name', 200);
			$table->string('email');
			$table->boolean('is_active');
			$table->string('password');
			$table->string('remember_token', 100)->nullable();
			$table->timestamps();
			$table->string('type', 10)->nullable();
			$table->string('title', 45)->nullable();
			$table->string('crm_user_id', 200);
			$table->smallInteger('dashboard_profile')->nullable()->default(0);
			$table->boolean('profile_completion_email')->nullable()->default(0);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
