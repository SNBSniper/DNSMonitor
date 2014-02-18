<?php

use jyggen\Curl;

class Server extends Eloquent{

	protected $guarded = array();
	
	public function urls()
	{
		return $this->belongsToMany('Url');
	}	

	public function clients()
	{
		return $this->belongsToMany('Client');
	}

	/**
	 * Scope Query to filter only the dns servers
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public function scopeDns($query)
	{
		return $query->whereType('dns');
	}

	/**
	 * Scope Query to filter only non-dns servers
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public function scopeNonDns($query)
	{
		return $query->where('type','=','slave')->orWhere('type','=','master');
	}

	/**
	 * Scope Query to filter only slave servers
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public function scopeSlave($query)
	{
		return $query->whereType('slave');
	}

	/**
	 * Fetch the current server
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public static function current()
	{
		return Server::whereIp( Config::get('app.ip') )->first();
	}

	/**
	 * Fetch the master server
	 * 
	 * @param  string $query  the original query
	 * @return query          the new scoped query
	 */
	public static function master()
	{
		return Server::whereType('master')->first();
	}

	public static function validate($input)
	{
		
		$rules = array(
			'ip'=>'required|ip',
			'provider'=>'required',
			'type'=>'required|in:dns,master,slave'
		);
		
		$v = Validator::make($input, $rules);	

		return $v;
	}

	/**
	 * 
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