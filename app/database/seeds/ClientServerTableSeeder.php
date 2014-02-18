<?php 
class ClientServerTableSeeder extends Seeder {

	public function run()
	{
		DB::table('client_server')->delete();
		$date = new DateTime;
		
			
		
		Client_server::create(array('status'=>1, 'server_id'=>'2', 'client_id'=>1 ,'created_at' => $date, 'updated_at' => $date));
		Client_server::create(array('status'=>1, 'server_id'=>'2', 'client_id'=>2 ,'created_at' => $date, 'updated_at' => $date));
		

		

		

	}
}