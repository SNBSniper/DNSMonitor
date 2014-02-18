<!DOCTYPE html>
<html>
  <head>
    <title>DNS Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->

    @section('css')
    <link href="css/bootstrap.min.css" rel="stylesheet">
    {{ HTML::style('packages/font-awesome/css/font-awesome.min.css') }}
    {{ HTML::style('style.css') }}
    <link rel="shortcut icon" href="favicon.ico" />
    @show
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{ url('/') }}">DNS Monitor</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ url('/servers') }}"><i class="fa fa-cloud"></i> Servidores</a></li>
        <li><a href="{{ url('clientss') }}"><i class="fa fa-user"></i> Clients</a></li>
        <li><a href="{{ url('/notificationss') }}"><i class="fa fa-bell-o"></i> Notifications</a></li>
      </ul>


       @if (is_null($application_started))
        <a href="{{ url('/init') }}" class="btn btn-warning navbar-btn pull-right"><i class="fa fa-cog"></i> Initialize Server</a>
       @endif

        <ul class="nav navbar-nav navbar-right">
            <li id="fat-menu" class="dropdown">
              <a href="#" id="server-info" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info"></i> Master Server Info <b class="caret"></b></a>
              <div class="dropdown-menu" role="menu" aria-labelledby="server-info" id="server-infoo">
                <b>Name:</b> {{ $master_server->provider }} <br>
                <b>IP:</b> {{ $master_server->ip }}
              </div>
            </li>
          </ul>
      
    </div><!-- /.navbar-collapse -->
    </div>
  </nav>
  <div class="container">
  	@yield('content')
  </div>
    @section('js')
    {{ HTML::script('js/jquery.min.js') }}
    {{ HTML::script('js/jquery-ui.min.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}
    
    @show
  </body>
</html>