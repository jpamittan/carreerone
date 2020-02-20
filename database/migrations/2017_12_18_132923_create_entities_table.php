<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entities', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('entity_name', 200);
			$table->string('entity_name_plural', 200);
			$table->string('description', 200)->nullable();
			$table->string('schema_name', 200);
			$table->string('logical_name', 200);
			$table->integer('object_type_code');
			$table->boolean('is_custom_entity');
			$table->string('ownership_type', 200);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entities');
	}

}
