<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuditTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('audit', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('context', 100);
			$table->string('code', 10);
			$table->string('trace', 4000)->nullable();
			$table->string('request_info', 4000)->nullable();
			$table->timestamps();
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
		Schema::drop('audit');
	}

}
