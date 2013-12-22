<?php 
class ServerUrlTableSeeder extends Seeder {

	public function run()
	{
		DB::table('server_url')->delete();
		
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
			'ip'=>'10.1.10.122',
			'port'=>'80',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		
		Server::create(array(
			'ip'=>'10.1.10.123',
			'port'=>'443',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));
		

	}
}