$(document).ready(function(){

     var getCronStatus = function(){
        $('div.status').each(function(){
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "//" + $this.data('server-ip') + "/api/v2/cron/status",
                crossDomain: true,
                dataType: "jsonp",
                contentType: "application/json",
                timeout: 5000,
                success: function(data) {
                    $this.removeClass('stopped');
                    $this.removeClass('running');
                    $this.removeClass('unreachable');
                    $this.addClass(data.status);
                },
                error: function(request, status, error) {
                    $this.addClass('unreachable');
                    console.log(error);
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
            jsonpCallback: 'callback',
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

    $('.client-draggable').draggable({
        cursor: "move",
        cursorAt: { top: 8, left: 10 },
        helper: function( event ) {
            return $( "<ul class='list-gruop dragging client-draggable'><li class='list-group-item'>"+ $(this).html() +"</li></ul>" );
        },
        appendTo: 'body'
    });

    $('.server-clients').droppable({
        accept: ".client-draggable",
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function( event, ui ) {
            var $this = $(this);
            var exists = $this.find("[data-client-id='" + ui.draggable.data('client-id') + "']").length != 0;

            if (exists) return;

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
    });

    $('.dnsServer-draggable').draggable({
        cursor: "move",
        cursorAt: { top: 8, left: 10 },
        helper: function( event ) {
            return $( "<ul class='list-gruop dragging dnsServer-draggable'><li class='list-group-item'>"+ $(this).html() +"</li></ul>" );
        },
        appendTo: 'body'
    });
    
    $('.dnsServer-droppable').droppable({
        accept: ".dnsServer-draggable",
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function( event, ui ) {
            var $this = $(this);
            var exists = $this.find("[data-dnsserver-id='" + ui.draggable.data('dnsserver-id') + "']").length != 0;

            if (exists) return;

            $this.append('<li class="list-group-item" id="loading"><i class="fa fa-spinner fa-spin"></i></li>');

            $.ajax({
                type: "POST",
                url: api_url+'/dns-servers',
                data: {"dnsserver_id" : ui.draggable.data('dnsserver-id'), "server_id" : $this.data("server-id") },
                dataType: "text",
                success: function(data) {
                    console.log(data); // show response from the php script.
                    $('#loading').remove();
                    $('<ul class="list-group"></ul>').append(
                        $( '<li class="list-group-item" data-dnsserver-id="'+ui.draggable.data('dnsserver-id')+'"></li>' ).text(  ui.draggable.text() ).append(
                            '<a href="#" class="remove-server pull-right"><i class="fa fa-times"></i></a>'
                        )
                    ).appendTo( $this );
                },
                error: function(request, status, error) {
                    $('#loading').remove();
                    generate_alert(error, 'danger');
                }
            });
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

    $(document).on('click', '.remove-dnsserver',  function(e){
        e.preventDefault();
        var $this = $(this);
        var dnsserver_id = $this.closest('li.list-group-item').data('dnsserver-id');
        var server_id = $this.closest('.dnsServer-droppable').data('server-id');

        $this.closest('li.list-group-item').hide();
        $this.closest('ul.list-group').append('<li class="list-group-item" id="loading"><i class="fa fa-spinner fa-spin"></i></li>');

        $.ajax({
            type: "DELETE",
            url: api_url+'/dns-servers',
            data: {"dnsserver_id" : dnsserver_id, "server_id" : server_id },
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
        setTimeout(function(){console.log('page load complete')}, 500);
        (function loopingFunction() {
            getCronStatus();
            setTimeout(loopingFunction, 15000);
        })();
    });
});