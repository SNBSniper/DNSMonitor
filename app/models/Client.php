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
		return $this->belongsToMany('Server');
	}
}