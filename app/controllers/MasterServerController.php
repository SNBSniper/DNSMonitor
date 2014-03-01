<?php

use jyggen\Curl;

class MasterServerController extends BaseController {

	function __construct() {
		// $this->beforeFilter('master');
	}

	public function ChangeRefreshRate()
	{
		$server_id = Input::get('server_id');
        $refresh_rate = Input::get('refresh_rate');

        $server = Server::find($server_id);
        $server->refresh_rate = $refresh_rate;

        // SEND SIGNAL TO THE SERVER
        $remote_response = Curl::get($server->ip . '/api/v2/change-refresh-rate/'. $refresh_rate);	

        if ($server->save()) {
            return Response::json(array(
                'error' => false,
                'msg'   => "Refresh rate changed to $refresh_rate for server $server_id",
                'server_id' => $server_id,
                'refresh_rate' => $refresh_rate,
                'remote_response' => $remote_response[0]->getContent()
            ));
        }
        return Response::json(array(
            'error' => true,
            'msg'   => "Couldn't change refresh rate for server $server_id"
        ));
	}

    public function addClientToServer()
    {
        $server_id = Input::get('server_id');
        $client_id = Input::get('client_id');
        $status    = Input::get('status', 1);

        $server = SlaveServer::find($server_id);

        $server->clients()->attach($client_id, array('status' => $status));

        return Response::json(array(
            'error' => false,
            'msg'   => "$client_id assigned to $server_id"
        ));
    }

    public function removeClientFromServer()
    {
        $server_id = Input::get('server_id');
        $client_id = Input::get('client_id');

        $server = SlaveServer::find($server_id);

        $server->clients()->detach($client_id);

        return Response::json(array(
            'error' => false,
            'msg'   => "$client_id unassigned from $server_id"
        ));
    }

    public function addNotification()
    {
        $client       = Client::find(Input::get('client_id'));
        $slaveServer  = SlaveServer::find(Input::get('slave_server_id'));
        $new_ip       = Input::get('new_ip');

        $notification_status = 0;

        if ( ! is_null($slaveServer) && ! is_null($new_ip) && ! is_null($client)) {
            DB::transaction(function() use ($slaveServer, $client, $new_ip, &$notification_status)
            {
                $notification = Notification::where('new_ip', '=', $new_ip)->first();
                
                if (is_null($notification)) {
                    // Create the new notification
                    $notification = new Notification(array(
                        'new_ip'     => $new_ip,
                        'client_id'  => $client->id
                    ));
                    $notification->save();
                    
                    $notification->notification_server()->attach($slaveServer->id);
                    $notification_status = 1;
                    Event::fire('notification.new.email', array($client->id));
                }else {
                    // Append the notification only. 
                    $already_notified = DB::table('notification_server')->where('server_id','=',$slaveServer->id)->where('notification_id','=',$notification->id)->first();
                   
                    if (is_null($already_notified)) {
                        $notification->notification_server()->attach($slaveServer->id);
                        $notification_status = 3;
                        Event::fire('notification.new.email', array($client->id));
                    }else {
                        $notification_status = 2;
                    }
                }
            });
        }

        return Response::json(array('status' => $notification_status));
    }

}
