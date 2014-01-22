<?php

class Ip extends Eloquent{

    protected $guarded = array();

	public function client()
	{
		return $this->belongsTo('Client');
	}

	public static function validate($input)
	{
		$rules = array('ip'=>'required|ip|foo:'.$input['server_id']);
		//$rules['publisher'] .= ',' . $publisher_id;
		$v = Validator::make($input, $rules);
	
		

		
		return $v;
	}
}