<?php

class Client_server extends Eloquent{

    protected $guarded = array();
    public $table = 'client_server';


    public function notifications()
    {
    	return $this->belongsToMany('Notification');
    }

    
}