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

}
