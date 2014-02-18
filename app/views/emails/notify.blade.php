<!DOCTYPE html>
<html lang="es-LA">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>New IP Found</h2>

        <div>
            <p>
                A new IP has been noticed for the client <b>{{$notification->client->name}}<sup>{{ $notification->client->id}}</sup>.</b>
            </p>
            <p>
                New IP: <b>{{ $notification->new_ip }}</b> <br>
                @foreach ($notification->notification_server as $server)
                    Found by: {{ $server->provider }} <sup>{{ $server->id }}</sup> § {{ $server->ip }} <br>
                @endforeach
            </p>
            {{ HTML::link('notifications', 'View all notifications') }}
        </div>
    </body>
</html>