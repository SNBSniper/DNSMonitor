<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servers', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('provider',50)->nullable();
			$table->string('type',30)->nullable();
			$table->string('ip',15);
			$table->unsignedInteger('port');
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
		Schema::drop('servers');
	}

}


