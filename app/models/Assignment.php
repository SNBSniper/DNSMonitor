<?php

class Assignment extends Eloquent{

    protected $guarded = array();

	public function server()
	{
		return $this->belongsTo('Server');
	}

}