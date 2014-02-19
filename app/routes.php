<?php

View::composer('*', function($view){
    $view->with('current_server', Server::current())
         ->with('master_server', Server::master())
         ->with('application_started', Application::where('started', '=', 1)->first());
});

Route::get('landing', function(){
    return View::make('landing')->with('ip', file_get_contents('http://phihag.de/ip/'));
});

Route::post('landing', function() {
    $input = Input::only(array('ip', 'provider'));
    $input['type'] = 'master';

    $validation = Server::validate($input);

    if ($validation->passes()) {
        DB::transaction(function() use ($input){
            $date = new \DateTime;
            $server = new Server($input);
            $server->save();
        });

        return Redirect::to('/')->with('success','Server Created Succesfully');
    }
    return Redirect::to('landing')->with('fail', $validation->messages);
});

Route::get('clientss',function(){
    $clients = Client::all();
    
    return View::make('clients')
        ->with('clients',$clients);
});

Route::post('create-client', function(){
    $input = Input::all();
    $validation = Client::validate($input);

    if ($validation->passes()) {
        DB::transaction(function() use ($input){
            $date = new \DateTime;
            $client = new Client($input);
            $client->save();
        });

        return Redirect::to('create-client')->with('success','Client Created Succesfully');
    }
    return Redirect::to('create-client')->with('fail', $validation->messages);
});

Route::get('create-client', function(){

    return View::make('create_client');
});

Route::get('servers',function(){
    $servers = Server::slave()->get();
    
    return View::make('servers')
        ->with('servers',$servers)
        ->with('clients', Client::all())
        ->with('current_server', Server::current());
});

Route::get('create-server', function() {
    $ip = Config::get('app.ip');
    return View::make('create_server')->with('ip', $ip);
});

Route::post('init-server', function(){
    $input = Input::all();
    $validation = Server::validate($input);
    
    if ($validation->passes()) {
        DB::transaction(function() use ($input)
        {
            $date = new \DateTime;
            $server = new Server($input);
            $server->port=80;
            $server->refresh_rate=15;
            $server->save();
            
        });

        return Redirect::to('create-server')->with('success','Server Created Succesfully');
    }
    else
        return Redirect::to('create-server')->with('fail', $validation->messages);
});

Route::get('/', function() {
    $currentServer = Server::current();

    if ($currentServer != null) {
        $clients_monitored = $currentServer->clients;

        // select ip, client_id from ips where ip IS NOT NULL group by ip;
        $ips = DB::table('ips')->select('ip','name')->whereNotNull('ip')->join('clients','clients.id','=','ips.client_id')->groupBy('ip')->orderBy('name','asc')->get();

        if( $currentServer->type == 'master' )
            return View::make('home')
                ->with('clients', Client::all())
                ->with('dns_servers', Server::dns()->get())
                ->with('ips', $ips);

        return View::make('slave-home')->with(array('ips'=>$ips, 'clients'=>$clients_monitored));
    }

    return Redirect::to('landing');
});

Route::get('notifications', function(){
    return View::make('notificationss')
        ->with('notifications', Notification::with(array('notification_server'))->orderBy('id', 'DESC')->paginate(20));
});

Route::get('start',function(){
    ini_set('max_execution_time', 300);

    

    $dns_servers = Server::dns()->get();

    $clients_monitored = Client::all();

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
        $application = new Application();
        $application->started = true;
        $application->save();
        return Redirect::to('/')->with('success', 'Application Initalized!');
    }
    else
    {
        return Redirect::to('/')->with('fail', 'Application Failed to Initalize');

    }
});

Route::get('init', function(){
    ini_set('max_execution_time', 0);

    $master_server = Server::where('type','=','master');
    $slave_servers = Server::where('type','=', 'slave')->get();
    $is_master = false;
    $ip = trim(file_get_contents("http://wtfismyip.com/text"));
    
    if ( ! is_null($master_server->first())) {
        if ($master_server->first()->ip == $ip) {
            $is_master = true;
        }
    }

    if (count($master_server->get()) != 1) {
        return View::make('init_1')->with('message','There can only be one Master Server');
    }
    else if (count($slave_servers) <=  0)
        return View::make('init_1')->with('message','There must be at least one Slave Server');

    return View::make('init_1')->with('success', 'Server May be Initialized' )
                               ->with('master_server', $master_server->first()) //Del this line!
                               ->with('slave_servers', $slave_servers)
                               ->with('is_master', $is_master);
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

Route::get('monitor-mock', function(){
    Log::info('The server started monitoring');
    // Monitor Proccess here ...
    Log::info('The server finished monitoring');

        // Case where new IP is detected
        $client_id       = mt_rand(1,3);
        $slave_server_id = mt_rand(1,3);
        $new_ip          = random_ip();
        
        // Send Notification to master server
        $response = Server::master()->notify($slave_server_id, $client_id, $new_ip);

        // Review the response given by the server
        if ($response == -1) {
            Log::error("The master server couldn't be notified");
            return Response::json(array('error' => true, 'msg' => "The master server couldn't be notified"));
        }else if ($response == 0) {
            Log::error("One or more required parameters (slave_server_id, client_id or new_ip) was missing or illegal");
            return Response::json(array('error' => true, 'msg' => "One or more required parameters (slave_server_id, client_id or new_ip) was missing or illegal"));
        }else if ($response == 2) {
            Log::error("The notification was already sent by this server");
            return Response::json(array('error' => true, 'msg' => "The notification was already sent by this server"));
        }else if ($response == 3) {
            Log::info("Notification already sent by pair slave server. Master server notified again");
            return Response::json(array('error' => false, 'msg' => "Notification already sent by pair slave server. Master server notified again"));
        }else if ($response == 1) {
            Log::info("The notification was sent successfully to the master server");
            return Response::json(array('error' => false, 'msg' => "The notification was sent successfully to the master server"));
        }

    return Response::json(array('error' => true, 'msg' => 'No changes found by this server'));
});

/**
 * Route Group for Master Server API
 */
Route::group(array('prefix' => 'api/v1'), function() {
    Route::post('change-refresh-rate', 'MasterServerController@changeRefreshRate');
    Route::post('notify', 'MasterServerController@addNotification');
    Route::post('clients', 'MasterServerController@addClientToServer');
    Route::delete('clients', 'MasterServerController@removeClientFromServer');
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