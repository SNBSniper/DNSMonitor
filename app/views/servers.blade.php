@extends('layouts.master')

@section('content')

<div class="row">
    <h1 class="page-header">Servers</h1>

    @foreach ($servers as $server)
    <div class="col-xs-12 col-sm-6 col-lg-4">
        <div class="box">                           
            <div class="icon">
                <div class="image {{ $server->type }}"><i class="fa fa-desktop"></i></div>
                <div class="info">
                    <h3 class="title">{{$server->ip}}<br><small>{{ ucwords($server->type) }}</small></h3>
                    <h6>{{ $server->provider }}</h6>
                    
                    @if ($server->type != 'master')
                    <p data-toggle="tooltip" data-placement="top" title="How often the server monitors DNS changes" class="tooltipp">
                        Refresh Rate: <span id="refresh-rate-{{ $server->id }}">{{ $server->refresh_rate }}</span> min
                    </p>
                    @endif
                    @foreach ($server->clients as $client)
                    <ul class="list-group">
                        <li class="list-group-item">{{ $client->name }} § {{ $client->hostname }}</li>
                    </ul>
                    @endforeach
                    @if ( $server->type != 'master')
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i> <span class="caret"></span>
                      </button>
                      
                      <ul class="dropdown-menu" role="menu">
                        <li><a data-server-id="{{ $server->id }}" href="#" class="launch-modal"><i class="fa fa-refresh"></i> Change Monitor Rate</a></li>
                        <li><a href="#"><i class="fa fa-bolt"></i> Send Monitor Signal</a></li>
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
    .box > .icon > .info > h3.title { font-size: 18px; color: #222; font-weight: 800; margin-bottom: 0; }
    .box > .icon > .info > h6 { color: #999; font-weight: 200; margin-top: 0; }
    .box > .icon > .info > h3.title > small { font-size: 16px; }
    .box > .icon > .info > p { font-size: 13px; color: #666; line-height: 1.5em; margin: 20px;}
    /*.box > .icon:hover > .info > h3.title, .box > .icon:hover > .info > p, .box > .icon:hover > .info > .more > a { color: #222; }*/
    .box > .icon > .info > .more a { font-family: "Roboto",sans-serif !important; font-size: 12px; color: #222; line-height: 12px; text-transform: uppercase; text-decoration: none; }
    /*.box > .icon:hover > .info > .more > a { color: #fff; padding: 6px 8px; background-color: #63B76C; }*/
    .box .space { height: 30px; }
</style>
@stop

@section('css')
@parent
<style>
    .list-group{ margin-bottom: 5px; font-size: 12px;}
    .list-group-item { padding: 2px 15px; }
</style>
@stop

@section('js')
@parent
<script>
$(function(){
    $('.tooltipp').tooltip();
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
});
</script>
@stop