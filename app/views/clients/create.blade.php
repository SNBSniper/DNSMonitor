@extends('layouts.master')

@section('content')
	<h1 class="page-header"><i class="fa fa-desktop"></i>  Add New Client</h1>
@if (Session::has('success'))
  <div class="alert alert-success">{{Session::get('success')}}</div>

@endif

@if (Session::has('fail'))
  <div class="alert alert-danger">{{Session::get('fail')}}</div>  
@endif
<div class="row">
	<div class="col-sm-6">
		<div class="panel-group" id="accordion">
		  <div class="panel panel-default">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
		          Client Data
		        </a>
		      </h4>
		    </div>
		    <div id="collapseOne" class="panel-collapse collapse in">
		      <div class="panel-body">
		        {{Form::open(array('url'=>'clients/create','method'=>"POST", 'role'=>'form'))}}
		            

		        <div class="form-group">
		            <label for="client-name"> Name</label>
		            {{Form::input('text', 'name', '', array('class'=>'form-control','placeholder'=>'Nombre Empresa'))}}
		        </div>
		        <div class="form-group">
		            <label for="provider">Hostname</label>
		            {{Form::input('text', 'hostname', '', array('class'=>'form-control','placeholder'=>'www.example.com'))}}
		        </div>

		        
		        {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
		        {{Form::close()}}
		      </div>
		    </div>
		  </div>
		</div>
	</div>	
</div>


@stop