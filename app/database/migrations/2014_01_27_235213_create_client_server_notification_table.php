<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


class CreateClientServerNotificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notification_server', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');			
			
			$table->unsignedInteger('server_id');
			$table->foreign('server_id')->references('id')->on('servers');

			$table->unsignedInteger('notification_id');
			$table->foreign('notification_id')->references('id')->on('notifications');
			
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
		Schema::drop('notification_server');
	}

}

