<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//Eloquent::unguard();

		$this->call('ClientTableSeeder');
		//$this->command->info('Client table seeded!');

		$this->call('UrlTableSeeder');
		//$this->command->info('URL table seeded!');

		$this->call('ServerTableSeeder');

		$this->call('ServerUrlTableSeeder');
		
		$this->call('ClientServerTableSeeder');

	}

}

