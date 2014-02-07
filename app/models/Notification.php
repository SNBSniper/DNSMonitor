<?php

class Notification extends Eloquent{

    protected $guarded = array();

	public function notification_server()
	{
		return $this->belongsToMany('Server');
	}

	public function client()
	{
		return $this->belongsTo('Client');
	}
}