<?php

class Client extends Eloquent{

    protected $guarded = array();

	public function urls()
	{
		return $this->hasMany('Url');
	}
}