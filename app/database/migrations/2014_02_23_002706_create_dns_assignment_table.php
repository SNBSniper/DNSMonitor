<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDnsAssignmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assignments', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');			

			$table->unsignedInteger('slave_server_id');
			$table->foreign('slave_server_id')->references('id')->on('servers');

			$table->unsignedInteger('dns_server_id');
			$table->foreign('dns_server_id')->references('id')->on('servers');
			
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
		Schema::drop('assignments');
	}

}
