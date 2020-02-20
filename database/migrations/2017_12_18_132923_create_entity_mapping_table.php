<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntityMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entity_mapping', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('entity_id');
			$table->string('logical_name', 250);
			$table->string('schema_name', 250);
			$table->string('display_name', 250);
			$table->integer('entity_type_id');
			$table->text('description', 65535);
			$table->boolean('custom_attribute');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entity_mapping');
	}

}
