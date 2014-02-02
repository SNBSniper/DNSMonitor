<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('name');
			$table->string('hostname');
			$table->integer('id_biller')->nullable();
			$table->integer('id_servicio')->nullable();
			$table->integer('version')->nullable();
			$table->integer('pil')->nullable();
			$table->integer('cod_tecnico')->nullable();
			$table->string('service_name')->nullable();
			$table->string('razon_social')->nullable();
			$table->string('categoria')->nullable();

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
		
		Schema::dropIfExists('clients');
		
	}

}
