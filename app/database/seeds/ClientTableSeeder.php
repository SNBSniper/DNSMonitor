<?php 
class ClientTableSeeder extends Seeder {

	public function run()
	{
		DB::table('clients')->delete();
		
		Client::create(array(
			'name'=>'Bookmart',
			'hostname'=>'www.bookmart.cl',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

		Client::create(array(
			'name'=>'Servipag',
			'hostname'=>'www.servipag.cl',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

		Client::create(array(
			'name'=>'Facebook',
			'hostname'=>'www.facebook.com',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

	}
}