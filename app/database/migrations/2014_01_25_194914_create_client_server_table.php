<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientServerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('client_server', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->boolean('status');

			$table->unsignedInteger('client_id');
			$table->foreign('client_id')->references('id')->on('clients');

			$table->unsignedInteger('server_id');
			$table->foreign('server_id')->references('id')->on('servers');
			
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
		Schema::drop('client_server');
	}

}