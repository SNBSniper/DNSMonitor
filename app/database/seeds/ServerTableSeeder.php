<?php 
class ServerTableSeeder extends Seeder {

	public function run()
	{
		DB::table('servers')->delete();
		
		Server::create(array(
			'ip'=>'10.1.10.120',
			'port'=>'80',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'ip'=>'10.1.10.121',
			'port'=>'80',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Server::create(array(
			'ip'=>'10.1.10.121',
			'port'=>'80',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		

	}
}