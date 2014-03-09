<?php

View::composer('*', function($view){
    $view->with('current_server', Server::current())
         ->with('master_server', MasterServer::first())
         ->with('menu_notifications', Notification::with('client')->take(5)->get())
         ->with('application_started', Application::where('started', '=', 1)->first());
});

Route::get('excel', function(){
    
    $inputFileName = public_path()."/uploads/data.xlsx";

    $rows= Excel::load($inputFileName, true)->toArray();
    
    foreach ($rows as $row) {
        $client = Client::where('name','=',$row['nombre_empresa'])->first();
        
        $url_string = $row['valor_parametro_cp'];
        $url_array = parse_url($url_string);
        
        if (!array_key_exists('host', $url_array)) {
            $url_string = "http://".$url_string;
        }

        $url_array = parse_url($url_string);
        $input = array();

        $input['ip'] = $url_array['host'];
        $validation = Ip::validate($input);
        
        if ($validation->passes()) {
            echo "Client has ip instead of hostname, ignoring: ".$input['ip']."<br>";
        }else {
            if(is_null($client)){

                $client = new Client();
                $client->name = $row['nombre_empresa'];
                $client->hostname = $url_array['host'];
                $client->id_canal_pago = $row['id_canal_pago'];
                $client->id_estado_canal = $row['id_estado_canal'];
                $client->id_biller = $row['id_biller'];
                $client->id_servicio = $row['id_servicio'];
                $client->Cod_Pil = $row['cod_pil'];
                $client->codigo_tecnocaja = $row['codigo_tecnocaja'];
                $client->nombre_servicio = $row['nombre_servicio'];
                $client->nombre_empresa = $row['rut'];
                $client->nombre_fantasia = $row['nombre_fantasia'];
                $client->rut = $row['rut'];
                $client->dv_rut = $row['dv_rut'];
                $client->ctactebch = $row['ctactebch'];
                $client->ctactebci = $row['ctactebci'];
                $client->id_tipo_parametro_cp = $row['id_tipo_parametro_cp'];
                $client->save();
                $url = new Url(array('link'=>$row['valor_parametro_cp']));
                $client->urls()->save($url);
            }else {
                $url = new Url(array('link'=>$row['valor_parametro_cp']));
                $client->urls()->save($url);
            }
        }   
    }
});

Route::get('landing', function(){

    return View::make('landing')->with('ip', file_get_contents('http://phihag.de/ip/'));
});

Route::post('landing', function() {
    $input = Input::only('ip', 'provider');
    $input['type'] = 'master';

    $validation = Server::validate($input);

    if ($validation->passes()) {
        DB::transaction(function() use ($input){
            $date = new \DateTime;
            $server = new Server($input);
            $server->port = 80;
            $server->save();
        });

        return Redirect::to('/')->with('success','Server Created Succesfully');
    }
    return Redirect::to('landing')->with('fail', $validation->messages);
});

Route::get('clients',function(){
    $clients = Client::all();
    
    return View::make('clients.index')
        ->with('clients',$clients)
        ->with('active', 'clients');
});

Route::post('clients/create', function(){
    $input = Input::only('name', 'hostname');
    $validation = Client::validate($input);

    if ($validation->passes()) {
        DB::transaction(function() use ($input){
            $date = new \DateTime;
            $client = new Client($input);
            $client->save();
        });

        return Redirect::to('clients/create')->with('success','Client Created Succesfully');
    }
    return Redirect::to('clients/create')->with('fail', $validation->messages);
});

Route::get('clients/create', function(){

    return View::make('clients.create')->with('active', 'clients');
});

Route::get('servers',function(){
    $servers = SlaveServer::all();
    
    return View::make('servers.index')
        ->with('servers',$servers)
        ->with('dnsServers', DnsServer::all())
        ->with('clients', Client::all())
        ->with('active', 'servers');
});

Route::get('servers/create', function() {
    $ip = Config::get('app.ip');
    return View::make('servers.create')->with('ip', $ip)->with('active', 'servers');
});

Route::post('servers/create', function(){
    $input = Input::only('ip', 'provider', 'type');
    $validation = Server::validate($input);
    
    if ($validation->passes()) {
        DB::transaction(function() use ($input)
        {
            $date = new \DateTime;
            $server = new Server($input);
            $server->port = 80;
            $server->refresh_rate = 15;
            $server->save();
        });

        return Redirect::to('servers/create')->with('success','Server Created Succesfully');
    }
    else
        return Redirect::to('servers/create')->with('fail', $validation->messages);
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

        return View::make('slave-home')->with(array('ips'=>$ips, 'clients' => $clients_monitored));
    }

    return Redirect::to('landing');
});

Route::get('notifications', function(){
    return View::make('notificationss')
        ->with('notifications', Notification::with(array('notification_server'))->orderBy('id', 'DESC')->paginate(20))
        ->with('active', 'notifications');
});

Route::get('start',function(){
    ini_set('max_execution_time', 300);

    $dns_servers       = DnsServer::all();
    $clients_monitored = Client::all();

    if ( is_null($clients_monitored) )
        return Redirect::to('/')->with('fail', 'Application Failed to Initalize');

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
                DB::transaction(function() use ($client,$dns_server)
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
});

Route::get('init', function(){
    ini_set('max_execution_time', 0);

    $master_server = MasterServer::all();
    $slave_servers = SlaveServer::all();
    $is_master = false;
    $ip = trim(file_get_contents("http://wtfismyip.com/text"));

    if (count($master_server) != 1) {
        return View::make('init_1')->with('message','There can only be one Master Server');
    }else if (count($slave_servers) <=  0)
        return View::make('init_1')->with('message','There must be at least one Slave Server');
    
    if ( ! is_null(MasterServer::first()) && MasterServer::first()->ip == $ip ) {
        $is_master = true;
    }

    return View::make('init_1')->with('success', 'Server May be Initialized' )
                               ->with('slave_servers', $slave_servers)
                               ->with('is_master', Server::current()->type == "master");
});

Route::get('monitor', function() {
    
    ini_set('max_execution_time', 0);
    Log::info('The server started monitoring');

    $slave_server = Server::current();

    if (is_null($slave_server))
        return Redirect::to('/')->with('fail', 'Slave Server was not found');

    $clients_monitored = $slave_server->clients;

    if (is_null($clients_monitored)) {
        return Redirect::to('/')->with('fail', 'Slave has no clients assigned to monitor');
    }

    //$dns_servers = Server::where('type','=','dns')->get();
    $dns_servers = $slave_server->assignedDns;

    if (is_null($dns_servers))
        return Redirect::to('/')->with('fail', 'No DNS Servers Found');

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
                        DB::transaction(function() use ($client,$dns_server,$input, $slave_server)
                        {
                            $date = new \DateTime;

                            $client->servers()->attach($dns_server->id, array('status'=>1,'created_at'=>$date, 'updated_at'=>$date));         
                            $client_server = DB::table('client_server')->where('server_id','=',$dns_server->id)->where('client_id','=',$client->id)->first();

                            $date = new \DateTime;
                            $ip = new Ip(array('ip'=>$input['ip'], 'client_id' => $client->id,'client_server_id'=>$client_server->id));
                            $ip->save();

                        });

                    }else{
                        //client_server exists, so we proceed to check it's ip records and see if there is a change
                        $ips = DB::table('ips')->where('client_server_id','=',$client_server->id)->get();
                        $found = false;
                        print_r($ips);
                        foreach ($ips as $ip) {
                            
                            if ($ip->ip == $input['ip']) {
                                $found = true;
                                break;
                            }
                        }

                        //$found = false; // simulate finding a new ip

                        if (!$found) { //if not found must notify master server WAY UNDER DEVELOPMENT

                            $master_server = Server::master();

                            $response = Server::master()->notify($slave_server->id, $client->id, $input['ip']);

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
    Log::info('The server finished monitoring');
});

Route::get('monitor-mock', function(){
    Log::info('The server started monitoring');
    // Monitor Proccess here ...
    Log::info('The server finished monitoring');

        // Case where new IP is detected
        $client_id       = mt_rand(1,2);
        $slave_server_id = 2;
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
    Route::delete('clients/dns-servers', 'MasterServerController@removeDnsServerFromServer');
    Route::post('clients/dns-servers', 'MasterServerController@addDnsServerToServer');
});

/**
 * Route Group for Slave Servers API
 */
Route::group(array('prefix' => 'api/v2'), function() {
    Route::get('change-refresh-rate/{rate?}', 'SlaveServerController@changeRefreshRate');
    Route::get('cron/status', 'SlaveServerController@getCronStatus');
    Route::get('cron/stop', 'SlaveServerController@stopCron');
    Route::get('cron/start', 'SlaveServerController@startCron');
    Route::get('monitor', 'SlaveServerController@monitor');
});