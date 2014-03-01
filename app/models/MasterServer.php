<?php

use jyggen\Curl;

class MasterServer extends Eloquent{

    protected $guarded = array();

	protected $table = 'servers';

	protected $attributes = array(
		'type' => 'master'
	);

	public function newQuery($excludeDeleted = true)
	{
		$query = parent::newQuery();
		$query->whereType('master');
		return $query;
	}

	/**
	 * Notify the master server of an ip change.
	 */
	public function notify($slave_server_id, $client_id, $new_ip)
	{
		$response = Curl::post( Server::master()->ip . '/api/v1/notify', array(
			'slave_server_id' => $slave_server_id,
			'client_id'       => $client_id,
			'new_ip'          => $new_ip
		));

		if (count($response) != 1)
			return -1; // The request was not done

		$response = json_decode($response[0]->getContent());
		return $response->status;
	}

}