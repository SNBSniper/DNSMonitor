<?php

class Client extends Eloquent{

    protected $guarded = array();

	public function urls()
	{
		return $this->hasMany('Url');
	}

	public function ips()
	{
		return $this->hasMany('Ip');
	}

	public function servers()
	{
		return $this->belongsToMany('Server')->withTimestamps();
	}

	public function dnsServers()
	{
		return $this->belongsToMany('DnsServer', 'client_server', 'client_id', 'server_id')->withTimestamps();
	}

	public function slaveServers()
	{
		return $this->belongsToMany('SlaveServer', 'client_server', 'client_id', 'server_id')->withTimestamps();
	}

	public static function validate($input)
	{
		
		$rules = array(
					'name'=>'required',
					'hostname'=>'required',
					);
		
		$v = Validator::make($input, $rules);	

		return $v;
	}

}