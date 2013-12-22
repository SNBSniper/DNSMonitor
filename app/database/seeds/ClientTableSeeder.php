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
			'hostname'=>'wwww.servipagl.cl',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
		));

	}
}