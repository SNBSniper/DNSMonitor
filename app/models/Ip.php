<?php

class Ip extends Eloquent{

    protected $guarded = array();

	public function client()
	{
		return $this->belongsTo('Client');
	}

	public static function validate($input)
	{

		$messages = array(
		    'foo' => 'the clinter_server row already exists',
		);
		//|foo:'.$input['server_id'].','.$input['client_id']
		$rules = array('ip'=>'required|ip');
		
		//$rules['publisher'] .= ',' . $publisher_id;
		$v = Validator::make($input, $rules,$messages);
		
		

		
		return $v;
	}
}