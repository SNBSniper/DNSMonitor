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
			$table->string('id_canal_pago')->nullable();
			$table->string('id_estado_canal')->nullable();
			$table->string('id_biller')->nullable();
			$table->string('id_servicio')->nullable();
			$table->string('Cod_Pil')->nullable();
			$table->string('codigo_tecnocaja')->nullable();
			$table->string('nombre_servicio')->nullable();
			$table->string('nombre_empresa')->nullable();
			$table->string('nombre_fantasia')->nullable();
			$table->string('rut')->nullable();
			$table->string('dv_rut')->nullable();
			$table->string('ctactebch')->nullable();
			$table->string('ctactebci')->nullable();
			$table->string('id_tipo_parametro_cp')->nullable();

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
