<?php 
class UrlTableSeeder extends Seeder {

	public function run()
	{
		DB::table('urls')->delete();
		
		Url::create(array(
			'link'=>'www.bookmart.cl/register',
			'client_id'=>'1',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

		Url::create(array(
			'link'=>'www.bookmart.cl/register/asdf',
			'client_id'=>'1',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

		Url::create(array(
			'link'=>'www.bookmart.cl/register/lole',
			'client_id'=>'1',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

		Url::create(array(
			'link'=>'www.servipag.cl/botonDePago/assosiation=1?id=1&monitoreo=2',
			'client_id'=>'2',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

		Url::create(array(
			'link'=>'www.servipag.cl/botonDePago/assosiation=1?id=2monitoreo=2',
			'client_id'=>'2',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

		Url::create(array(
			'link'=>'www.servipag.cl/botonDePago/assosiation=1?id=2&monitoreo=2',
			'client_id'=>'2',
			'created_at' => new DateTime,
			'updated_at' => new DateTime
			));

	}
}