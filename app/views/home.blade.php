@extends('layouts.master')

@section('content')

<style type="text/css">
	
</style>
HelloWorld!! WELCOME TO DNS MONITOR AAHHA! <br>
@foreach ($clients as $client) 
	
	
	{{$client->name}} , {{$client->hostname}}<br>

	<ul>
	@foreach ($client->urls()->get() as $url)
		<li>{{$url->link}}</li>	
	@endforeach
	</ul>
@endforeach

<ul>
	@foreach ($servers as $server)

	<li>{{$server->ip}}:{{$server->port}}={{$server->type}}</li>
@endforeach	
</ul>

<h1>Urls that this server is monitoring</h1>


<ul>
	@foreach ($urls as $url)
		<li>{{$url->link}}</li> 
	@endforeach	
</ul>

@stop