<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRefreshRateToServersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('servers', function(Blueprint $table) {
			$table->integer('refresh_rate')->unsigned()->default(15)->nullable;
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('servers', function(Blueprint $table) {
			$table->dropColumn('refresh_rate');
		});
	}

}
