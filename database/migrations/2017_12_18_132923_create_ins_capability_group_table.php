<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsCapabilityGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ins_capability_group', function(Blueprint $table)
		{
			$table->integer('id');
			$table->string('group_name');
			$table->string('group_images', 2000)->nullable();
			$table->integer('order_by');
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
		Schema::drop('ins_capability_group');
	}

}
