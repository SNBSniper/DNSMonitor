@extends('layouts.master')

@section('content')


<h1 class="page-header"><i class="fa fa-desktop"></i> This is a {{ $server->type }} server <small>IP: {{$server->ip}}:{{$server->port}} </small></h1>

<div class="panel panel-default">
  	<div class="panel-heading">
    	<h3 class="panel-title"><i class="fa fa-users"></i> Clients monitored by this server ({{count($clients)}})</h3>
  	</div>
  	<div class="panel-body">
		@if (isset($clients))
			<ul>
			@foreach ($clients as $client)
				<li>{{$client->name}}</li>
			@endforeach
			</ul>
		@else

		@endif
  	</div>
</div>

<div class="panel panel-default">
  	<div class="panel-heading">
    	<h3 class="panel-title"><i class="fa fa-search"></i> IP/Hostnames being monitored by this server</h3>
  	</div>
  	<div class="panel-body">
  		<dl class="dl-horizontal">
		@foreach ($ips as $client)
			<dt>{{$client->name}}</dt>
			<dd>{{$client->ip}}</dd>
		@endforeach
		</dl>
  	</div>

  
</div>

@stop