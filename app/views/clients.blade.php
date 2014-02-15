@extends('layouts.master')

@section('content')

	<div class="row padding">
		<div class="col-sm-6">
			{{HTML::link('create-client', 'Agregar Cliente', array('class'=>'btn btn-primary'))}}		
		</div>
		
	</div>

	<br>

	<div class="row">
		<div class="col-sm-6">
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
	</div>
@stop