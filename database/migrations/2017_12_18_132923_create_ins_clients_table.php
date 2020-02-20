<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_clients', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id');
			$table->integer('clientid');
			$table->integer('age')->nullable();
			$table->string('company', 100)->nullable();
			$table->dateTime('dateofbirth')->nullable();
			$table->string('email', 50)->nullable();
			$table->string('firstname', 50)->nullable();
			$table->string('gender', 20)->nullable();
			$table->string('individualnumber', 100)->nullable();
			$table->integer('mobile_number')->nullable();
			$table->integer('phone_number')->nullable();
			$table->string('insnewsletteremail', 1)->default('N');
			$table->string('jobpreference1', 50)->nullable();
			$table->string('jobpreference2', 50)->nullable();
			$table->string('jobpreference3', 50)->nullable();
			$table->string('linkedinurl', 100)->nullable();
			$table->string('name', 100)->nullable();
			$table->text('qualifications', 65535)->nullable();
			$table->string('sectorfocus', 50)->nullable();
			$table->string('skypeid', 50)->nullable();
			$table->string('surname', 50)->nullable();
			$table->string('testimonial', 1)->default('N');
			$table->string('title', 100)->nullable();
			$table->string('ownerid', 100)->nullable();
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
		Schema::drop('ins_clients');
	}

}
