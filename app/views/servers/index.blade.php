@extends('layouts.master')

@section('content')

<div class="row">
    @if ($current_server->type == 'master')
        {{HTML::link('servers/create', 'Create Server', array('class'=>'btn btn-primary'))}}
    @endif
    <h1 class="page-header">Servers </h1> 

    <div id="alert-box"></div>
    @foreach ($servers as $server)
    <div class="col-xs-12 col-sm-6 col-lg-4">
        <div class="box">                           
            <div class="icon">
                <div class="image status waiting" data-server-ip="{{ $server->ip }}"><i class="fa fa-desktop"></i></div>
                <div class="info">
                    <h3 class="title">{{$server->ip}}<br><small>{{ ucwords($server->type) }}</small></h3>
                    <h6>{{ $server->provider }}</h6>
                    
                    <p data-toggle="tooltip" data-placement="top" title="How often the server monitors DNS changes" class="tooltipp">
                        Refresh Rate: <span id="refresh-rate-{{ $server->id }}">{{ $server->refresh_rate }}</span> min
                    </p>

                    
                    
                    <ul class="nav nav-pills nav-justified">
                        <li class="active"><a href="#clients-{{ $server->id }}" data-toggle="tab">Clients</a></li>
                        <li><a href="#dnsServers-{{ $server->id }}" data-toggle="tab">Dns Servers</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="clients-{{ $server->id }}">
                            <div class="server-list server-clients" data-server-id="{{ $server->id }}">
                                @foreach ($server->clients as $client)
                                <ul class="list-group">
                                    <li class="list-group-item" data-client-id="{{ $client->id }}">{{ $client->name }} § {{ $client->hostname }}
                                    @if ($current_server->type == 'master')
                                        <a href="#" class="remove-client pull-right"><i class="fa fa-times"></i></a>
                                    @endif
                                    </li>
                                </ul>
                                @endforeach
                            </div>        
                        </div>
                        <div class="tab-pane fade" id="dnsServers-{{ $server->id }}">
                            <div class="server-list dnsServer-droppable" data-server-id="{{ $server->id }}">
                                @foreach ($server->assignedDns as $dns)
                                <ul class="list-group">
                                    <li class="list-group-item" data-dnsServer-id="{{ $dns->id }}">{{ $dns->provider }} § {{ $dns->ip }}
                                    @if ($current_server->type == 'master')
                                        <a href="#" class="remove-dnsserver pull-right"><i class="fa fa-times"></i></a>
                                    @endif
                                    </li>
                                </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    @if ( $current_server->type == 'master')
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i> <span class="caret"></span>
                      </button>
                      
                      <ul class="dropdown-menu" role="menu" style="text-align: left;">
                        <li><a data-server-id="{{ $server->id }}" href="#" class="launch-modal"><i class="fa fa-refresh fa-fw"></i> Change Monitor Rate</a></li>
                        <li><a href="#" class="start-monitor" data-server-ip="{{ $server->ip }}"><i class="fa fa-bolt fa-fw"></i> Start Monitor</a></li>
                        <li><a href="#" class="stop-monitor" data-server-ip="{{ $server->ip }}"><i class="fa fa-minus-circle fa-fw"></i> Stop Monitor</a></li>
                      </ul>
                    </div>
                    @endif
                </div>
            </div>
            <div class="space"></div>
        </div> 
    </div>
    @endforeach
</div>

@if ($current_server->type == 'master')

<div class="slide-panel" style="top: 80px; z-index: 3;">
    <a href="#" class="slide-panel-toggle btn btn-default" style="left: 254px; top: 27px;"><i class="fa fa-user"></i> Clients</a>
    <div class="slide-panel-content">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control input-sm slide-panel-filter" placeholder="Filter&hellip;">
        </div>

        @foreach ($clients as $client)
        <ul class="list-group tooltipp slide-panel-list" data-toggle="tooltip" data-placement="top" title="Drag me to a server so it monitors me">
            <li class="list-group-item client-draggable" data-client-id="{{ $client->id }}">{{ $client->name }} § {{ $client->hostname }}</li>
        </ul>
        @endforeach
    </div>
</div>

<div class="slide-panel">
    <a href="#" class="slide-panel-toggle btn btn-default"><i class="fa fa-cloud"></i> Dns Servers</a>
    <div class="slide-panel-content">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control input-sm slide-panel-filter" placeholder="Filter&hellip;">
        </div>

        @foreach ($dnsServers as $dnsServer)
        <ul class="list-group tooltipp slide-panel-list" data-toggle="tooltip" data-placement="top" title="Drag me to a server so it uses me">
            <li class="list-group-item dnsServer-draggable" data-dnsServer-id="{{ $dnsServer->id }}">{{ $dnsServer->provider }} § {{ $dnsServer->ip }}</li>
        </ul>
        @endforeach
    </div>
</div>

<div class="modal fade" id="change-refresh-rate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Change Monitor Rate</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => 'api/v1/change-refresh-rate', 'method' => 'post', 'id' => 'change-refresh-rate-form')) }}
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                {{ Form::label('refresh_rate', 'Refresh Rate') }}
                                <input type="text" class="form-control" name="refresh_rate" id="refresh_rate" value="15">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="server_id" id="server_id" value="0">
                    <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {{ Form::submit('Save changes', array('class' => 'btn btn-primary')) }}
                {{ Form::close() }}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- <img src="http://emerginganabaptist.com/wp-content/uploads/2012/12/Big-Bang.jpg" alt="big"> -->
@endif

@stop

@section('css')
@parent
{{ HTML::style('css/slide-panel.css') }}
<style>
    .box > .icon { text-align: center; position: relative; }
    .box > .icon > .waiting {  background: #adadad; }
    .box > .icon > .stopped {  background: #3e7eb7; }
    .box > .icon > .running {  background: #63B76C; }
    .box > .icon > .unreachable {  background: #df1916; }
    .box > .icon > .image { position: relative; z-index: 2; margin: auto; width: 88px; height: 88px; border: 8px solid white; line-height: 88px; border-radius: 50%; vertical-align: middle; }
    /*.box > .icon:hover > .image { background: #333; }*/
    .box > .icon > .image > i { font-size: 36px !important; color: #fff !important; }
    /*.box > .icon:hover > .image > i { color: white !important; }*/
    .box > .icon > .info { margin-top: -24px; background: rgba(0, 0, 0, 0.04); border: 1px solid #e0e0e0; padding: 15px 6px 10px 6px; }
    /*.box > .icon:hover > .info { background: rgba(0, 0, 0, 0.04); border-color: #e0e0e0; color: white; }*/
    .box > .icon > .info > h3.title { font-size: 18px; color: #222; font-weight: 800; margin-bottom: 0; }
    .box > .icon > .info > h6 { color: #999; font-weight: 200; margin-top: 0; }
    .box > .icon > .info > h3.title > small { font-size: 16px; }
    .box > .icon > .info > p { font-size: 13px; color: #666; line-height: 1.5em; margin: 20px;}
    /*.box > .icon:hover > .info > h3.title, .box > .icon:hover > .info > p, .box > .icon:hover > .info > .more > a { color: #222; }*/
    .box > .icon > .info > .more a { font-family: "Roboto",sans-serif !important; font-size: 12px; color: #222; line-height: 12px; text-transform: uppercase; text-decoration: none; }
    /*.box > .icon:hover > .info > .more > a { color: #fff; padding: 6px 8px; background-color: #63B76C; }*/
    .box .space { height: 30px; }

    .list-group{ margin-bottom: 5px; font-size: 12px;}
    .list-group-item { padding: 2px 15px; }

    .server-list { min-height: 30px; margin-bottom: 10px; padding: 6px;}
    .ui-state-hover { background: green; }
    .ui-state-default { background: yellow; }

    .dragging { max-width: 280px; text-align: center; z-index: 10;}

    .tab-content { margin-top: 10px;}
</style>

@stop

@section('js')
@parent
@if($current_server->type == 'master')
<script>
    $(function(){ $('.tooltipp').tooltip(); });
    var api_url = '<?php echo url('api/v1/clients'); ?>';
</script>
{{ HTML::script('js/slide-panel.js') }}
{{ HTML::script('js/server.js') }}
@endif
@stop