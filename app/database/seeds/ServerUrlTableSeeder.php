<?php 
class ServerUrlTableSeeder extends Seeder {

	public function run()
	{
		DB::table('server_url')->delete();
		
		$clients = Client::all();

		foreach ($clients as $client) {
			
			$urls = $client->urls()->get();

			foreach ($urls as $url) {
						$server_urls = 
							array(
								array('server_id'=>'2', 'url_id'=>$url->id ,'created_at' => new DateTime, 'updated_at' => new DateTime),
								array('server_id'=>'3', 'url_id'=>$url->id ,'created_at' => new DateTime, 'updated_at' => new DateTime),
								array('server_id'=>'4', 'url_id'=>$url->id ,'created_at' => new DateTime, 'updated_at' => new DateTime),
								array('server_id'=>'5', 'url_id'=>$url->id ,'created_at' => new DateTime, 'updated_at' => new DateTime),
								);
							
						
						DB::table('server_url')->insert($server_urls);
						
					}
		}

		

	}
}