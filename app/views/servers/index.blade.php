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

                    <div class="server-clients" data-server-id="{{ $server->id }}">
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
                    @if ( $current_server->type == 'master')
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i> <span class="caret"></span>
                      </button>
                      
                      <ul class="dropdown-menu" role="menu" style="text-align: left;">
                        <li><a data-server-id="{{ $server->id }}" href="#" class="launch-modal"><i class="fa fa-refresh"></i> Change Monitor Rate</a></li>
                        <li><a href="#" class="start-monitor" data-server-ip="{{ $server->ip }}"><i class="fa fa-bolt"></i> Start Monitor</a></li>
                        <li><a href="#" class="stop-monitor" data-server-ip="{{ $server->ip }}"><i class="fa fa-minus-circle"></i> Stop Monitor</a></li>
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
<div class="clients-container">
    <a href="#" class="btn btn-success" id="toggle-clients">
        <i class="fa fa-users"></i> Clients
    </a>
    <div class="clients">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-search"></i></span>
          <input type="text" class="form-control input-sm" placeholder="Filter&hellip;" id="clients-filter">
        </div>
        <br>

        @foreach ($clients as $client)
        <ul class="list-group tooltipp" data-toggle="tooltip" data-placement="top" title="Drag me to a server so it monitors me">
            <li class="list-group-item" data-client-id="{{ $client->id }}">{{ $client->name }} § {{ $client->hostname }}</li>
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
@endif

@stop

@section('css')
@parent
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
    .box > .icon > .info { margin-top: -24px; background: rgba(0, 0, 0, 0.04); border: 1px solid #e0e0e0; padding: 15px 0 10px 0; }
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


    /* Clients List */
    #toggle-clients { position: absolute; left: 253px; top: 27px; -webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg); -ms-transform: rotate(-90deg); -o-transform: rotate(-90deg); transform: rotate(-90deg); }
    .clients-container { position: fixed; top: 80px; left: -280px; width: 280px; z-index: 2; }
    .clients { background: #f3f3f3; border: 1px solid #e0e0e0; padding: 20px; text-align: center; max-height: 400px; overflow: scroll; }
    .clients-hover { left: 0; }
    .clients ul { cursor: move; }
    .server-clients { padding: 6px; min-height: 30px; margin-bottom: 10px; }
    .ui-state-hover { background: yellow; }
    .ui-state-default { background: lightblue; }
</style>

@stop

@section('js')
@parent

<script>
    $(function(){
        $('.tooltipp').tooltip();
    });
</script>
@if($current_server->type == 'master')
<script>
$(document).ready(function(){
    var api_url = '<?php echo url('api/v1/clients'); ?>';

     var getCronStatus = function(){
        $('div.status').each(function(){
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "//" + $this.data('server-ip') + "/api/v2/cron/status",
                crossDomain: true,
                contentType: "application/javascript",
                dataType: 'jsonp',
                timeout: 5000,
                success: function(data) {
                    $this.removeClass('stopped');
                    $this.removeClass('running');
                    $this.addClass(data.status)
                },
                error: function(request, status, error) {
                    $this.addClass('unreachable');
                }
            });
        });
    }

    var sendCronSignal= function (to, action, callback) {
        $.ajax({
            type: "GET",
            url: "//" + to + "/api/v2/cron/" + action,
            crossDomain: true,
            contentType: "application/javascript",
            dataType: 'jsonp',
            timeout: 5000,
            success: function(data) {
                callback(data, false);
            },
            error: function(request, status, error) {
                callback(error, true);
            }
        });
    }

    var generate_alert =  function (msg, type) {
        var alert  = '<div class="alert alert-'+ type +' alert-dismissable">';
            alert += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
            alert += msg + '</div>';

        $(alert).appendTo('#alert-box');
    }
    
    $('.launch-modal').on('click', function(e){
        var $this = $(this);
        e.preventDefault();
        $('#change-refresh-rate').modal();
        $('#server_id').val($this.data('server-id'));
        $('#refresh_rate').val($('#refresh-rate-'+$this.data('server-id')).html());
        
    });

    $('#change-refresh-rate-form').submit(function(e){
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: $("#change-refresh-rate-form").serialize(), // serializes the form's elements.
            success: function(data) {
                console.log(data); // show response from the php script.
                $('#change-refresh-rate').modal('hide');
                $('#refresh-rate-' + data.server_id).html(data.refresh_rate);
            }
        });
        return false;
    });

    $('#clients-filter').keyup(function(){
       var valThis = $(this).val();
        $('.clients > ul > li').each(function(){
         var text = $(this).text().toLowerCase();
            (text.indexOf(valThis) == 0) ? $(this).show() : $(this).hide();         
       });
    });

    $('.clients > ul > li').draggable({
        cursor: "move",
        cursorAt: { top: 8, left: 10 },
        helper: function( event ) {
            return $( "<ul class='list-gruop'><li class='list-group-item'>"+ $(this).html() +"</li></ul>" );
        },
        appendTo: 'body'
    });

    $('.server-clients').droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function( event, ui ) {
            var $this = $(this);
            var exists = $this.find("[data-client-id='" + ui.draggable.data('client-id') + "']").length != 0;

            if ( ! exists) {

                $this.append('<li class="list-group-item" id="loading"><i class="fa fa-spinner fa-spin"></i></li>');

                $.ajax({
                    type: "POST",
                    url: api_url,
                    data: {"client_id" : ui.draggable.data('client-id'), "server_id" : $this.data("server-id") },
                    dataType: "text",
                    success: function(data) {
                        console.log(data); // show response from the php script.
                        $('#loading').remove();
                        $('<ul class="list-group"></ul>').append(
                            $( '<li class="list-group-item" data-client-id="'+ui.draggable.data('client-id')+'"></li>' ).text(  ui.draggable.text() ).append(
                                '<a href="#" class="remove-client pull-right"><i class="fa fa-times"></i></a>'
                            )
                        ).appendTo( $this );
                    },
                    error: function(request, status, error) {
                        $('#loading').remove();
                        generate_alert(error, 'danger');
                    }
                });
            }
            
        }
    });
    
    $(document).on('click', '.remove-client',  function(e){
        e.preventDefault();
        var $this = $(this);
        var client_id = $this.closest('li.list-group-item').data('client-id');
        var server_id = $this.closest('.server-clients').data('server-id');

        $this.closest('li.list-group-item').hide();
        $this.closest('ul.list-group').append('<li class="list-group-item" id="loading"><i class="fa fa-spinner fa-spin"></i></li>');

        $.ajax({
            type: "DELETE",
            url: api_url,
            data: {"client_id" : client_id, "server_id" : server_id },
            dataType: "text",
            success: function(data) {
                console.log(data); // show response from the php script.
                $this.closest('ul.list-group').remove();
            },
            error: function(request, status, error) {
                $('#loading').remove();
                $this.closest('li.list-group-item').show();
                generate_alert(error, 'danger');
            }
        });
    });

    $("#toggle-clients").on('click', function(e){
        $('.clients-container').stop(true,true).toggleClass( "clients-hover", 1000, "easeOutExpo" );
        e.preventDefault();
    });

    $('.start-monitor').on('click', function(e) {
        var $this = $(this);
        e.preventDefault();
        sendCronSignal($this.data('server-ip'), 'start', function(d, timeout){
            if(timeout) {
                generate_alert("The server "+ $this.data('server-ip') +" did not respond", "danger");
                return;
            }
            generate_alert(d.msg, 'success');
            var status = $this.closest('div.icon').children('.status');
        
            status.removeClass('stopped');
            status.removeClass('unreachable');
            status.addClass('running');
        });
        
    });

    $('.stop-monitor').on('click', function(e) {
        var $this = $(this);
        e.preventDefault();
        sendCronSignal($this.data('server-ip'), 'stop', function(d, timeout){
            if(timeout) {
                generate_alert("The server "+ $this.data('server-ip') +" did not respond", "danger");
                return;
            }
            generate_alert(d.msg, 'success');

            var status = $this.closest('div.icon').children('.status');
            status.removeClass('running');
            status.removeClass('unreachable');
            status.addClass('stopped');
        });
        
    });

    $(window).bind('load', function(){
        setTimeout(function(){getCronStatus();}, 500);
        setInterval(getCronStatus, 90000); // refresh every 1.5 min
    });
});
</script>
@endif
@stop