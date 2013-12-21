<?php

class Client extends Eloquent{


	public function urls()
	{
		return $this->hasMany('Url');
	}
}