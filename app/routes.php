<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    
    $local_ip = gethostbyname(trim(`hostname`));

    $local_server = Server::where('ip','=',$local_ip)->first();
    
    $urls = $local_server->urls()->get();
    
	$clients = Client::all();
    $servers = Server::all();
	
	

	return View::make('home')->with(array('clients'=>$clients,'servers'=>$servers, 'urls'=>$urls));
});

Route::get('init', function(){
    ini_set('max_execution_time', 300);
    $local_ip = gethostbyname(trim(`hostname`));
    $server = Server::where('ip','=',$local_ip)->first();
    
    $urls = $server->urls()->get();

    foreach ($urls as $url) {
        $exists = urlExists($url->link);
        $url->link_status = $exists;
        $url->save();
    }

    $dns_servers = Server::where('type','=','dns')->get();


    $clients = Client::all();
    foreach ($clients as $client) {
        foreach ($dns_servers as $dns_server) {
            $hostName = $client->hostname;
            $result = `nslookup $hostName $dns_server->ip` ;
            $result = trim($result);
            $result = strtolower($result);
            $final = explode('\n',$result);
            preg_match('/address: (.*)/', $result, $matches);
            
            if ( array_key_exists(1, $matches) )
            {
                $input =  array( 'ip' => $matches[1], 'server_id'=>$dns_server->id,  );
                    
                $validation = Ip::validate($input);
                
                if ($validation->passes()) {

                    $ip = new Ip ($input);
                    $ip->status=1;
                    
                    $client->ips()->save($ip);
                }
                else
                    echo $matches[1].' already exists <br> ' ;
                
            }
            else
            {
                $input =  array( 'ip' => null, 'server_id'=>$dns_server->id,'status'=>0 );
                $ip = new Ip ($input);

                $client->ips()->save($ip);


            }

        }

        
    }

    

});

Route::get('monitor', function(){
    
});



function urlExists($url=NULL)  
{  
    if($url == NULL) return false;  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $data = curl_exec($ch);  
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch);  
    if($httpcode>=200 && $httpcode<300){  
        return true;  
    } else {  
        return false;  
    }  
}


