<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntityMappingAdditionalDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entity_mapping_additional_data', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('entity_mapping_id');
			$table->string('type', 200);
			$table->string('name', 200);
			$table->string('value', 200);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entity_mapping_additional_data');
	}

}
