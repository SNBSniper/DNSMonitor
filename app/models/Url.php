<?php

class Url extends Eloquent{

	protected $guarded = array();
	
	public function client()
	{
		return $this->BelongsTo('Client');
	}

	public function servers()
	{
		return $this->belongsToMany('Server');
	}
}