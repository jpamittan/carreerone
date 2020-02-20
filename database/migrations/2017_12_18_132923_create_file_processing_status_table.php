<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFileProcessingStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_processing_status', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('file_name');
			$table->dateTime('process_start')->nullable();
			$table->dateTime('process_end')->nullable();
			$table->boolean('process_status')->default(0);
			$table->string('process_message', 500)->nullable();
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
		Schema::drop('file_processing_status');
	}

}
