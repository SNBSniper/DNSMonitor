@extends('layouts.landing')

@section('content')

<h1 class="page-header"><i class="fa fa-desktop"></i> Setup Up Master Server</h1>
@if (Session::has('success'))
  <div class="alert alert-success">{{Session::get('success')}}</div>
@endif

@if (Session::has('fail'))
  <div class="alert alert-danger">{{Session::get('fail')}}</div>  
@endif

<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Master Server
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
        {{Form::open(array('url'=>'landing','method'=>"POST", 'role'=>'form'))}}
          <div class="form-group">
              <label for="ip-master"> IP Address</label>
              {{ Form::input('text', 'ip', $ip, array('class' => 'form-control')) }}
          </div>
          <div class="form-group">
              <label for="provider">Provider</label>
              {{Form::input('text', 'provider', '', array('class'=>'form-control','placeholder'=>'Proveedor'))}}
          </div>

          {{Form::submit('Create Master Server', array('class'=>'btn btn-default'))}}
        {{Form::close()}}
      </div>
    </div>
  </div>

  
</div>

@stop