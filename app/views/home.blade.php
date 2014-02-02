@extends('layouts.master')

@section('content')

<style type="text/css">
	
</style>
<h1>This is a {{$server->type}} server </h1>
<h2>IP: {{$server->ip}}:{{$server->port}} </h2>
<h3>Clients Monitored by this server ({{count($clients)}})</h3>

@if (isset($clients))
	@foreach ($clients as $client) 
		
		
		<b>{{$client->name}}</b><br>
		
		
	@endforeach

@else

@endif


<h4> IP/Hostnames being monitored by this server</h4>
@foreach ($ips as $client) 
	
	
	{{$client->name}} , {{$client->ip}}<br>
	
	<ul>
	
	</ul>
@endforeach






@stop