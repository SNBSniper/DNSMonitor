<?php

class SlaveServer extends Eloquent{

    protected $guarded = array();

	protected $table = 'servers';

    protected $attributes = array(
        'type' => 'slave'
    );

	public function newQuery($excludeDeleted = true)
	{
		$query = parent::newQuery();
		$query->whereType('slave');
		return $query;
	}

    public function clients()
    {
        return $this->belongsToMany('Client', 'client_server', 'server_id', 'client_id');
    }

    public function assignedDns(){
        return $this->belongsToMany('Server', 'assignments', 'slave_server_id', 'dns_server_id');
    }

    public function monitor()
    {
        foreach ($this->clients as $client) {
            foreach($this->assignedDns as $dnsServer) {
                $ip = $this::nslookup($client->hostname, $dnsServer->ip);

                if ( $ip ) {
                    Log::info("nslookup WORKED for $client->hostname using $dnsServer->ip");
                    
                    /* Validate the IP, if it fails, skip to the next DNS server */
                    $v = Ip::validate(array('ip' => $ip));
                    if ($v->fails()){
                        Log::error("Invalid IP address: $ip");
                        continue;
                    }

                    $client_server = Client_server::where('server_id', '=', $dnsServer->id)->where('client_id', '=', $client->id)->first();

                    if ( is_null($client_server) ) {
                        //client_server doesn't exist, so we add the new server to the client
                        DB::transaction(function() use ($client,$dnsServer,$ip)
                        {
                            $client->DnsServers()->attach($dnsServer->id, array('status' => 1));
                            $client_server = DB::table('client_server')->where('server_id','=',$dnsServer->id)->where('client_id','=',$client->id)->first();

                            $ip = new Ip(array('ip' => $ip, 'client_id' => $client->id,'client_server_id' => $client_server->id));
                            $ip->save();
                        });
                    }else {
                        // The client_server exists, so we must check for the known ips for the client against the new ip obtained by nslookup
                        $found = in_array($ip, $client->ips()->lists('ip'));

                        if ( ! $found) {
                            Log::info('New IP Detected. trying to notify the master server');
                            // Notify the master server that a new ip has been detected
                            $master_server = Server::master();

                            $response = MasterServer::first()->notify($this->id, $client->id, $ip);

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
                        }else {
                            Log::info("The ip is already on our records");
                            return Response::json(array('error' => true, 'msg' => 'No changes found by this server'));
                        }
                    }
                }else {
                    Log::info("nslookup FAILED for $client->hostname using $dnsServer->ip");

                    /* For Status History */
                    DB::transaction(function() use ($client,$dnsServer)
                    {
                        $client->dnsServers()->attach($dnsServer->id, array('status' => 0));
                    });
                }
            } // End foreach dnsServers
        } // End foreach clients
    }

    public static function nslookup($hostName = "www.google.com", $dnsServer = "8.8.8.8")
    {
        $result = `nslookup $hostName $dnsServer` ;
        $result = trim($result);
        $result = strtolower($result);

        preg_match('/address: (.*)/', $result, $matches);

        if ( array_key_exists(1, $matches) )
            return $matches[1];
        return null;
    }

}