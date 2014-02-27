@extends('layouts.master')

@section('content')

<h1 class="page-header"><i class="fa fa-desktop"></i>  Server Set Up Configuration</h1>
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
        {{Form::open(array('url'=>'servers/create','method'=>"POST", 'role'=>'form'))}}
          <div class="form-group">
              <label for="ip-master"> IP Address</label>
              {{ Form::input('text', 'ip', $ip, array('class' => 'form-control')) }}
          </div>
          <div class="form-group">
              <label for="provider">Provider</label>
              {{Form::input('text', 'provider', '', array('class'=>'form-control','placeholder'=>'Proveedor'))}}
          </div>

          <div class="form-group">
              <label for="provider">Server Type</label>
              {{Form::select('type', array('slave'=>'Slave Master', 'dns' => 'DNS Server'), 'slave', array('class'=>'form-control'))}}
          </div>
              
          {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
        {{Form::close()}}
      </div>
    </div>
  </div>

  
</div>

@stop