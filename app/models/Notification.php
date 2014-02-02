<?php

class Notification extends Eloquent{

    protected $guarded = array();

	public function client_server()
	{
		return $this->belongsToMany('Client_server');
	}

	public function client()
	{
		return $this->belongsTo('Client');
	}
}