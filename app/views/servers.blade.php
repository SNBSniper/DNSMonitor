@extends('layouts.master')

@section('content')

<div class="row">
<h1 class="page-header">Server View</h1>

@foreach ($servers as $server)
<div class="col-xs-12 col-sm-6 col-lg-4">
    <div class="box">                           
        <div class="icon">
            <div class="image {{ $server->type }}"><i class="fa fa-desktop"></i></div>
            <div class="info">
                <h3 class="title">Servidor: {{$server->ip}} <br> {{ ucwords($server->type) }}</h3>
                <p>
                    @foreach ($server->clients()->get() as $client)
                    
                    @endforeach
                </p>
                <h4></h4>
                @foreach ($server->clients()->get() as $client)
                <ul class="list-group">
                  <li class="list-group-item"><b>Nombre del Cliente:</b> {{$client->name}}</li>
                  <li class="list-group-item"><b>Hostname:</b> {{$client->hostname}}</li>
                </ul>
                @endforeach
            </div>
        </div>
        <div class="space"></div>
    </div> 
</div>
@endforeach
</div>

@stop

@section('css')
@parent
<style>
    .box > .icon { text-align: center; position: relative; }
    .box > .icon > .master {  background: #3e7eb7; }
    .box > .icon > .slave {  background: #63B76C; }
    .box > .icon > .image { position: relative; z-index: 2; margin: auto; width: 88px; height: 88px; border: 8px solid white; line-height: 88px; border-radius: 50%; vertical-align: middle; }
    /*.box > .icon:hover > .image { background: #333; }*/
    .box > .icon > .image > i { font-size: 36px !important; color: #fff !important; }
    /*.box > .icon:hover > .image > i { color: white !important; }*/
    .box > .icon > .info { margin-top: -24px; background: rgba(0, 0, 0, 0.04); border: 1px solid #e0e0e0; padding: 15px 0 10px 0; }
    /*.box > .icon:hover > .info { background: rgba(0, 0, 0, 0.04); border-color: #e0e0e0; color: white; }*/
    .box > .icon > .info > h3.title { font-size: 18px; color: #222; font-weight: 800; }
    .box > .icon > .info > p { font-size: 13px; color: #666; line-height: 1.5em; margin: 20px;}
    /*.box > .icon:hover > .info > h3.title, .box > .icon:hover > .info > p, .box > .icon:hover > .info > .more > a { color: #222; }*/
    .box > .icon > .info > .more a { font-family: "Roboto",sans-serif !important; font-size: 12px; color: #222; line-height: 12px; text-transform: uppercase; text-decoration: none; }
    /*.box > .icon:hover > .info > .more > a { color: #fff; padding: 6px 8px; background-color: #63B76C; }*/
    .box .space { height: 30px; }
</style>
@stop