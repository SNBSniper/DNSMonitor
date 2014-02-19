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