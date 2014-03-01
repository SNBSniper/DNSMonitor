<?php

class DnsServer extends Eloquent{

    protected $guarded = array();

	protected $table = 'servers';

	protected $attributes = array(
		'type' => 'dns'
	);

	public function newQuery($excludeDeleted = true)
	{
		$query = parent::newQuery();
		$query->whereType('dns');
		return $query;
	}

	public function clients() {
		return $this->belongsToMany('Client', 'client_server', 'server_id', 'client_id');
	}

	public function assignedSlaveServers() {
		return $this->belongsToMany('Server', 'assignments', 'dns_server_id', 'slave_server_id');
	}
}