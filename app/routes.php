<?php

Route::get('servers',function(){
    $servers = Server::nonDns()->get();
    
    return View::make('servers')->with('servers',$servers);
});

Route::get('/', function()
{
    $currentServer = Server::current();

    $clients_monitored = $currentServer->clients;

    // select ip, client_id from ips where ip IS NOT NULL group by ip;
    $ips = DB::table('ips')->select('ip','name')->whereNotNull('ip')->join('clients','clients.id','=','ips.client_id')->groupBy('ip')->orderBy('name','asc')->get();

    if( $currentServer->type == 'master' )
        return View::make('home')
            ->with('clients', Client::all())
            ->with('servers', Server::all())
            ->with('server', Server::current())
            ->with('ips', $ips);

    return View::make('home')->with(array('ips'=>$ips, 'server'=>$currentServer, 'clients'=>$clients_monitored));
});

Route::get('notificationss', function(){
    return View::make('notificationss')
        ->with('notifications', Notification::orderBy('id', 'DESC')->get());
});

Route::get('init', function(){
    ini_set('max_execution_time', 300);
    
    $slave_server = Server::current();
    
    /*$urls = $slave_server->urls()->get();

    foreach ($urls as $url) {
        $exists = urlExists($url->link);
        $url->link_status = $exists;
        $url->save();
    }*/

    $dns_servers = Server::dns()->get();

    $clients_monitored = get_server_clients($slave_server);

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
    $server_id = Input::get('server_id');

    $clients_id = DB::table('client_server')->select('client_id')->where('server_id','=',$server_id)->get();
    
    $result = array();

    foreach ($clients_id as $key => $value) {
        
        // explode the sub-array, and add the parts
        array_push($result, $value->client_id);
        
    }

    
    
    return json_encode($result);


    
});

Route::get('monitor', function(){
    
    ini_set('max_execution_time', 300);
    $local_ip = gethostbyname($_SERVER['SERVER_ADDR']);
    $local_ip = '192.168.0.105';
    $slave_server = Server::where('ip','=',$local_ip)->first();


    $clients_monitored = get_server_clients($slave_server);

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

                            $date = new \DateTime;
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

                            $master_server = get_master_server();

                            //$query_url = $master_server->ip."/notifications"."?slave_server_id=".$slave_server->id."&ip=".$input['ip'];
                            
                            $query_url = "10.1.10.149"."/notifications"."?slave_server_id=".$slave_server->id."&ip=".$input['ip'];
                            $lurl=get_fcontent($query_url);
                            
                            
                            $json_output = json_decode($lurl[0]);
                            
                            if (!is_null($json_output)) {
                                switch ($json_output->status) {
                                    case 1:
                                        echo ('successfuly notfied ip: '.$input['ip']."<br>");
                                        break;
                                    case 0: 
                                        echo ('ip: '.$input['ip']." could not be notified <br>");
                                        break;
                                    case 2:
                                        echo ('ip: '.$input['ip']."already notified by this server <br>");
                                        break;
                                    default:
                                        echo $json_output->status;
                                        dd('unknown error')    ;
                                        break;
                                }   
                            }
                            else
                                echo $lurl[0];
                            

                            

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

                    $date = new \DateTime;

                    $client->servers()->attach($dns_server->id, array('status'=>0,'created_at'=>$date, 'updated_at'=>$date));

                    

                });


            }

        }

        
    }
    
    return View::make('monitor');


});

Route::get('notifications',function(){

    //verify that get parameters client_server_id and ip exist!
    $slave_server = Server::find(Input::get('slave_server_id'));
    
    $input['ip'] = Input::get('ip');
    //dd(array('1'=>$slave_server, '2'=>$input));

    $notification_status = 0;
    if (!is_null($slave_server) && !is_null($input['ip'])) {
        DB::transaction(function() use ($slave_server, $input, &$notification_status)
        {
            //$notification = Notification::where('client_id','=',$slave_server->client_id)->first();

            /*$date = new DateTime;

            
            $from = new DateTime;
            
            $to = new DateTime;
            date_add($to, date_interval_create_from_date_string('30 min'));
            date_sub($from, date_interval_create_from_date_string('30 min'));


            $notification = Notification::whereBetween('created_at', array($from, $to))->where('client_id','=',$slave_server->client_id)->first();   */
            $notification = Notification::where('new_ip', '=', $input['ip'])->first();
            
            if (is_null($notification)) {
                
                    
                $timestamp = new DateTime();
                $date = $timestamp->format('Y-m-d H:i:s');
                
                $notification = new Notification(array(
                                'new_ip'=>$input['ip'],
                                'client_id'=>$slave_server->id,
                                'created_at' => $date,
                                'updated_at' => $date
                                
                            ));
                $notification->save();
                
                $notification->notification_server()->attach($slave_server->id, array('created_at' => $date,'updated_at' => $date));
                $notification_status=1;
                
            }
            else
            {
                $row = DB::table('notification_server')->where('server_id','=',$slave_server->id)->where('notification_id','=',$notification->id)->first();
                $timestamp = new DateTime();
                $date = $timestamp->format('Y-m-d H:i:s');

                if (is_null($row)) {
                    $notification->notification_server()->attach($slave_server->id, array('created_at' => $date,'updated_at' => $date));    
                    $notification_status = 1;
                }
                else{
                    $notifications_status = 2;
                }
                

            }
            
            
        });
    
    }

    return Response::json(array('status'=>$notification_status));
    
    

});

Route::get('master', function(){
  
    $master_server = Server::where('type','=','master')->first();
    $response = array();
    $response['ip']=$master_server->ip;
    return Response::json($response);


/*    $notifications = Notification::all();
    foreach ($notifications as $notification) {

        $ip = Ip::where('ip','=',$notification->old_ip)->first();
        $client = Client::find($ip->client_id);


    }*/
});

Route::get('slave_sync', function(){
    $results = array();

    $slave_ip = gethostbyname($_SERVER['REMOTE_ADDR']);

    $slave_ip = '10.1.10.149'; //developinggg
    $slave_server = Server::where('ip','=',$slave_ip)->first();

    $dns_servers = Server::where('type','=','dns')->select(array('id','ip'))->get();
    $results['dns_servers'] = $dns_servers->toArray();

    $clients_monitored = get_server_clients($slave_server);
    $results['clients_monitored'] = $clients_monitored->toArray();

    return Response::json($results);
});

Route::get('clients', function(){
    $server_id = Input::get('server_id');

    $clients_id = DB::table('client_server')->select('client_id')->where('server_id','=',$server_id)->lists('client_id');

    return Response::json($clients_id);
});

Route::get('monitor-mock', function(){
    Log::info('The server started monitoring');
    $not = new Notification;
    $not->new_ip = '0.0.0.0';
    $not->client_id = 3;
    $not->save();
    Log::info('The server finished monitoring');
    return Response::json(array('notifications' => true ));
});

/**
 * Route Group for Master Server API
 */
Route::group(array('prefix' => 'api/v1'), function() {
    Route::post('change-refresh-rate', 'MasterServerController@changeRefreshRate');
});

/**
 * Route Group for Slave Servers API
 */
Route::group(array('prefix' => 'api/v2'), function() {
    Route::get('change-refresh-rate/{rate?}', 'SlaveServerController@changeRefreshRate');
    Route::get('cron/status', 'SlaveServerController@getCronStatus');
    Route::get('cron/stop', 'SlaveServerController@stopCron');
    Route::get('cron/start', 'SlaveServerController@startCron');
});