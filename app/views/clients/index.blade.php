@extends('layouts.master')

@section('content')
	<div class="row">
		<h1 class="page-header">Clients</h1>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title"><i class="fa fa-users"></i> Clients</h2>
			</div>

			<div class="panel-body">
				<ul>
					@foreach ($clients as $client)
						<li>{{$client->name}}</li>	
					@endforeach
				</ul>
			</div>
			
		</div>
	</div>
@stop