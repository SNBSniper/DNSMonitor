<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ips', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('ip',15)->nullable();
			$table->unsignedInteger('client_id');
			$table->foreign('client_id')->references('id')->on('clients');

			$table->unsignedInteger('client_server_id');
			$table->foreign('client_server_id')->references('id')->on('client_server');
			
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
		Schema::drop('ips');
	}

}