<?php

class Server extends Eloquent{

	protected $guarded = array();
	
	public function urls()
	{
		return $this->belongsToMany('Url');
	}	

	public function clients()
	{
		return $this->belongsToMany('Client');
	}

	/**
	 * Scope Query to extract only the master server
	 * 
	 * @param  string $query  the original query
	 * @return query          the new scoped query
	 */
	public function scopeMaster($query)
	{
		return $query->whereType('master')->first();
	}

	/**
	 * Scope Query to filter only the dns servers
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public function scopeDns($query)
	{
		return $query->whereType('dns');
	}

	/**
	 * Scope Query to filter only non-dns servers
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public function scopeNonDns($query)
	{
		return $query->where('type','=','slave')->orWhere('type','=','master');
	}

	/**
	 * Scope Query to fetch the current server
	 * 
	 * @param  query $query the original query
	 * @return query        the scoped query
	 */
	public function scopeCurrent($query)
	{
		return $query->whereIp( Config::get('app.ip') )->first();
	}
}