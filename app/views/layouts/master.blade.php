<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DNS Monitor</title>

    @section('css')
    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('packages/font-awesome/css/font-awesome.min.css') }}
    {{ HTML::style('css/style.css') }}
    {{ HTML::style('css/sb-admin.css') }}
    @show
    <link rel="shortcut icon" href="/favicon.ico" />
</head>

<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}}">DNS Monitor</a>
            </div>
            <!-- /.navbar-header -->


            <ul class="nav navbar-top-links navbar-right">

                @if (is_null($application_started))
                    <a href="{{ url('/init') }}" class="btn btn-warning navbar-btn"><i class="fa fa-cog"></i> Initialize App</a>
                @endif

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-desktop fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="//{{ $master_server->ip }}">
                                <div>
                                    <strong>{{ $master_server->provider}}</strong>
                                    <span class="pull-right text-muted">
                                        <em>{{ $master_server->ip }}</em>
                                    </span>
                                </div>
                                <div>Master Server</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="{{ url('servers') }}">
                                <strong>View All Servers</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell-o fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                     @foreach ($menu_notifications as $notification)
                        <li>
                            <a href="{{ url('notifications') }}">
                                <div>
                                    <strong><i class="fa fa-info fa-fw"></i> New IP Found!</strong>
                                    <span class="pull-right text-muted">
                                        <em>{{ $notification->new_ip }}</em>
                                    </span>
                                </div>
                                <div>A new IP as been found for client {{ $notification->client->name }}</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li class="divider"></li>
                     @endforeach
                        
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Notifications</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
            </ul>
            <!-- /.navbar-top-links -->

        </nav>
        <!-- /.navbar-static-top -->

        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li>
                        <a href="{{ url('/') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                    </li>
                    <li{{ isset($active) && $active == 'servers' ? ' class="active"' : '' }}>
                        <a href="{{ url('servers') }}"><i class="fa fa-cloud fa-fw"></i> Servers<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="{{ url('servers') }}"><i class="fa fa-desktop fa-fw"></i> View Slave Servers</a>
                            </li>
                            @if ($current_server->type == 'master')
                            <li>
                                <a href="{{ url('servers/create') }}"><i class="fa fa-plus fa-fw"></i> Create Server</a>
                            </li>
                            @endif
                            <li>
                                <a href="#"><i class="fa fa-file-text fa-fw"></i> View Log File</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li{{ isset($active) && $active == 'clients' ? ' class="active"' : '' }}>
                        <a href="{{ url('clients') }}"><i class="fa fa-user fa-fw"></i> Clients<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="{{ url('clients') }}"><i class="fa fa-user fa-fw"></i> View Clients</a>
                            </li>
                            <li>
                                <a href="{{ url('clients/create')}}"><i class="fa fa-plus fa-fw"></i> Create Clients</a>
                            </li>
                        </ul>
                    </li>
                    <li{{ isset($active) && $active == 'notifications' ? ' class="active"' : '' }}>
                        <a href="{{ url('notifications') }}"><i class="fa fa-bell-o fa-fw"></i> Notifications</a>
                    </li>
                </ul>
                <!-- /#side-menu -->
            </div>
            <!-- /.sidebar-collapse -->
        </nav>
        <!-- /.navbar-static-side -->

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    @yield('content')
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    @section('js')
    {{ HTML::script('js/jquery.min.js') }}
    {{ HTML::script('js/jquery-ui.min.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}
    {{ HTML::script('js/jquery.metisMenu.js') }}
    {{ HTML::script('js/sb-admin.js') }}
    @show
</body>

</html>
