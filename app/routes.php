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
    
    $local_ip = gethostbyname($_SERVER['SERVER_ADDR']);
    $local_ip = '10.1.10.149';
    $local_server = Server::where('ip','=',$local_ip)->first();
    

    
    if( $local_server->type == 'master' )
    {
        
        $clients = Client::all();
        $servers = Server::all();

        return View::make('home')->with(array('clients'=>$clients,'servers'=>$servers));
    }
    
    else
    {
        $clients_monitored = get_server_clients($local_server);

        
        // select ip, client_id from ips where ip IS NOT NULL group by ip;
        $ips = DB::table('ips')->select('ip','name')->whereNotNull('ip')->join('clients','clients.id','=','ips.client_id')->groupBy('ip')->orderBy('name','asc')->get();

        return View::make('home')->with(array('ips'=>$ips, 'server'=>$local_server, 'clients'=>$clients_monitored));
        
    }
	

	
	

	
});

Route::get('init', function(){
    ini_set('max_execution_time', 300);
    $local_ip = gethostbyname($_SERVER['SERVER_ADDR']);
    $local_ip = '10.1.10.149';

    $local_server = Server::where('ip','=',$local_ip)->first();
    
    /*$urls = $local_server->urls()->get();

    foreach ($urls as $url) {
        $exists = urlExists($url->link);
        $url->link_status = $exists;
        $url->save();
    }*/

    $dns_servers = Server::where('type','=','dns')->get();

    
    $clients_monitored = get_server_clients($local_server);

    if (!is_null($clients_monitored)) {

        foreach ($clients_monitored as $client) {

            foreach ($dns_servers as $dns_server) {

                $hostName = $client->hostname;
                $result = `nslookup $hostName $dns_server->ip` ;
                $result = trim($result);
                $result = strtolower($result);
                preg_match('/address: (.*)/', $result, $matches);
                
                if ( array_key_exists(1, $matches) ) // if an nslookup returns a value then validate it
                {
                    $input =  array( 'ip' => $matches[1]);

                    
                    $validation = Ip::validate($input);
                    
                    if ($validation->passes()) { // if it passes validation then insert it
                        
                        $row = DB::table('client_server')->where('server_id','=',$dns_server->id)->where('client_id','=',$client->id)->first();
                        
                        if (is_null($row)) { //check to see if the client_server row exists, if it doesn't create it, if it does then attach it

                            DB::transaction(function() use ($client,$dns_server,$input)
                            {
                                $date = new \DateTime;

                                $client->servers()->attach($dns_server->id, array('status'=>1,'created_at'=>$date, 'updated_at'=>$date));         
                                $row = DB::table('client_server')->where('server_id','=',$dns_server->id)->where('client_id','=',$client->id)->first();

                                $ip = new Ip(array('ip'=>$input['ip'], 'client_id'=>$client->id,'client_server_id'=>$row->id));
                                $ip->save();

                            });

                        }
                        else{
                            // There should be none created since this is an initialization, only the if above should be going in.
                        }
                    }
                    else{
                        dd($validation->messages()->get());
                    }
                    
                }
                else // nslookup failed so we add status=0 to the client_server row to note that the server failed the lookup.
                {
                    DB::transaction(function() use ($client,$dns_server,$input)
                    {
                        $client->servers()->attach($dns_server->id, array('status'=>0));         
                    });


                }

            }

            
        }
        return "Initilization Complete";
    }
    else
    {
        return "Server is not assigned Clients to monitor";
    }
    
    

});

Route::get('get_clients',function(){
    dd(Input::all());
});

Route::get('monitor', function(){
    echo file_get_contents('10.1.10.149/notifications');
    dd('stop');
    ini_set('max_execution_time', 300);
    $local_ip = gethostbyname($_SERVER['SERVER_ADDR']);
    $local_ip = '10.1.10.149';
    $local_server = Server::where('ip','=',$local_ip)->first();


    $clients_monitored = get_server_clients($local_server);

    $dns_servers = Server::where('type','=','dns')->get();

    foreach ($clients_monitored as $client) {
        foreach ($dns_servers as $dns_server) {
            $hostName = $client->hostname;
            $result = `nslookup $hostName $dns_server->ip` ;
            $result = trim($result);
            $result = strtolower($result);

            preg_match('/address: (.*)/', $result, $matches);
                            
            if ( array_key_exists(1, $matches) ) //nslookup, if it returns a value, then validate it
            {
                $input =  array( 'ip' => $matches[1]);

                
                $validation = Ip::validate($input);
                
                if ($validation->passes()) { //validation passes then insert it
                    
                    //get the current server that is monitoring a specific client
                    $client_server = Client_server::where('server_id','=',$dns_server->id)->where('client_id','=',$client->id)->first();
                    
                    if (is_null($client_server)) {
                        //client_server doesn't exist, so we add the new server to the client


                        DB::transaction(function() use ($client,$dns_server,$input)
                        {
                            $date = new \DateTime;

                            $client->servers()->attach($dns_server->id, array('status'=>1,'created_at'=>$date, 'updated_at'=>$date));         
                            $client_server = DB::table('client_server')->where('server_id','=',$dns_server->id)->where('client_id','=',$client->id)->first();

                            $ip = new Ip(array('ip'=>$input['ip'], 'client_id'=>$client->id,'client_server_id'=>$client_server->id));
                            $ip->save();

                        });

                    }
                    else{
                        //client_server exists, so we proceed to check it's ip records and see if there is a change
                        $ips = DB::table('ips')->where('client_server_id','=',$client_server->id)->get();
                        $found = false;

                        foreach ($ips as $ip) {
                            
                            if ($ip->ip == $input['ip']) {
                                $found = true;
                            }
                        }

                        $found = false; // simulate finding a new ip

                        if (!$found) { //if not found must notify master server WAY UNDER DEVELOPMENT

                            DB::transaction(function() use ($client_server, $input)
                            {
                                //$notification = Notification::where('client_id','=',$client_server->client_id)->first();
                                $master_server = get_master_server();
                                $ip = $master_server->ip;
                                $ip = '10.1.10.149';

                                /*$date = new DateTime;

                                
                                $from = new DateTime;
                                
                                $to = new DateTime;
                                date_add($to, date_interval_create_from_date_string('30 min'));
                                date_sub($from, date_interval_create_from_date_string('30 min'));


                                $notification = Notification::whereBetween('created_at', array($from, $to))->where('client_id','=',$client_server->client_id)->first();   */
                                $notification = Notification::where('new_ip', '=', $input['ip'])->first();
                                
                                if (is_null($notification)) {
                                    

                                    $date = new \DateTime;

                                    $notification = new Notification(array(
                                                    'new_ip'=>$input['ip'],
                                                    'client_id'=>$client_server->client_id,
                                                    'created_at' => new DateTime,
                                                    'updated_at' => new DateTime
                                                    
                                                ));
                                    $notification->save();
                                    //$client->servers()->attach($dns_server->id, array('status'=>0));         
                                    
                                    $notification->client_server()->attach($client_server->id, array('created_at' => new DateTime,'updated_at' => new DateTime));

                                    
                                }
                                else
                                {
                                    $row = DB::table('client_server_notification')->where('client_server_id','=',$client_server->id)->where('notification_id','=',$notification->id)->first();
                                    

                                    if (is_null($row)) {
                                        $notification->client_server()->attach($client_server->id, array('created_at' => new DateTime,'updated_at' => new DateTime));    
                                    }
                                    

                                }
                                
                                
                            });
                        
                            

                        }
                        else
                            echo "No change";

                    }
                }
                else{
                    dd($validation->messages()->get());
                }
                
            }
            else
            {
                DB::transaction(function() use ($client,$dns_server,$input)
                {
                    $client->servers()->attach($dns_server->id, array('status'=>0));         
                    

                });


            }

        }

        
    }


});

Route::get('notifications',function(){
    return Response::json(array('name' => 'Steve', 'state' => 'CA'));

});

Route::get('master', function(){
    
    $notifications = Notification::all();
    foreach ($notifications as $notification) {

        $ip = Ip::where('ip','=',$notification->old_ip)->first();
        $client = Client::find($ip->client_id);


    }
});



function get_master_server()
{
    return Server::where('type','=','master')->first();


}

function get_server_clients($local_server)
{
    $clients_id = DB::table('client_server')->select('client_id')->where('server_id','=',$local_server->id)->get();

    
    
    $result = array();

    foreach ($clients_id as $key => $value) {
        
        // explode the sub-array, and add the parts
        array_push($result, $value->client_id);
        
    }
    $clients_monitored = NULL;
    if (count($result)>0) {
        $clients_monitored = Client::whereIn('id',$result)->get();
    }
    
    
    return $clients_monitored;


}



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
