@extends('layouts.master')

@section('content')
<h1>Server View</h1>

@foreach ($servers as $server)
	<h2>Servidor: {{$server->ip}} -- {{$server->type}}</h2> 
	@foreach ($server->clients()->get() as $client)
		Nombre del Cliente: {{$client->name}} <br>
		Hostname: {{$client->hostname}}
	@endforeach
@endforeach

@stop