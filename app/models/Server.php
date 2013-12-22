<?php

class Server extends Eloquent{

	protected $guarded = array();
	
	public function urls()
	{
		return $this->belongsToMany('Url');
	}	
}