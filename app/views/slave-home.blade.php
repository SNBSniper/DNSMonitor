@extends('layouts.master')

@section('content')

  <h1 class="page-header"><i class="fa fa-desktop"></i> This is a {{ $current_server->type }} server <small>{{$current_server->ip}}:{{$current_server->port}} </small></h1>


  @if (Session::has('success'))
    <div class="alert alert-success">{{Session::get('success')}}</div>

  @endif

  @if (Session::has('fail'))
    <div class="alert alert-danger">{{Session::get('fail')}}</div>  
  @endif


 <div class="row">
     <div class="col-sm-6">
         <div class="panel panel-default">
             <div class="panel-heading">
                 <h3 class="panel-title"><i class="fa fa-users"></i> Clients ({{ count($clients) }})</h3>
             </div>
             <div class="panel-body panel-scroll">
                 <ul>
                 @foreach ($clients as $client)
                     <li>{{ $client->name }}</li>
                 @endforeach
                 </ul>
             </div>
         </div>
     </div>
 </div>
 <div class="row">
     <div class="col-sm-12">
         <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-search"></i> IP/Hostnames being monitored</h3>
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
     </div>
 </div>



@stop

@section('css')
@parent
<style>
    i.green { color: yellowgreen; }
    i.red   { color: #FF4136; }
    .panel-scroll { height: 300px; overflow: scroll; }
</style>
@stop