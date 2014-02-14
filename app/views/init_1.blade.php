@extends('layouts.master')
@section('content')



<h1 class="page-header"><i class="fa fa-desktop"></i>  Initalize Application</h1>
@if (isset($message))
  <div class="alert alert-danger">{{$message}}</div>  
@endif

@if (isset($success))
  <div class="alert alert-success">{{$success}}</div>  
	

	@if (isset($master_server))
		<div class="panel-group" id="accordion">
		  <div class="panel panel-default">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Master Server</a>
		      </h4>
		    </div>
		    <div id="collapseOne" class="panel-collapse collapse in">
		      <div class="panel-body">
				
				{{$master_server->provider}}
				{{$master_server->ip}}
				
		      </div>
		    </div>
		  </div>  
	</div>
	@endif

	@if (isset($slave_servers))
		<div class="panel-group" id="accordion">
		  <div class="panel panel-default">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Slave Servers</a>
		      </h4>
		    </div>
		    <div id="collapseTwo" class="panel-collapse collapse in">
		      <div class="panel-body">
				
				@foreach ($slave_servers as $slave_server)
					{{$slave_server->provider}}
					{{$slave_server->ip}} <br>
				@endforeach
				
		      </div>
		    </div>
		  </div>  
		</div>
	@endif
	<br>
	@if (isset($is_master))
		@if ($is_master)
			{{HTML::link('start', 'Start Application', array('class'=>'btn btn-primary'))}}	
		@else
			<span class="label label-default">Start this application from Master Server</span>
		@endif
	@endif
	
@endif


@stop







