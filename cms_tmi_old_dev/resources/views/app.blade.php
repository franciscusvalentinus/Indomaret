<!DOCTYPE html>
<html lang="en">
<head>         
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CMS TMI</title>

	<link href="{{ url('css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{ url('css/bootstrap-theme.css')}}" rel="stylesheet">
	<link href="{{ url('css/elegant-icons-style.css')}}" rel="stylesheet">
	{{--<link href="{{ url('css/font-awesome.css')}}" rel="stylesheet">--}}
	<link href="{{ url('css/bootstrap-fullcalendar.css')}}" rel="stylesheet">
	<link href="{{ url('css/jquery.easy-pie-chart.css')}}" rel="stylesheet">
	<link href="{{ url('css/owl.carousel.css')}}" rel="stylesheet">
	<link href="{{ url('css/igr.css')}}" rel="stylesheet">
	<link href="{{ url('css/select2.min.css')}}" rel="stylesheet">
	<link href="{{ url('css/fileinput.min.css')}}" rel="stylesheet">
	<link href="{{ url('css/editor.dataTables.min.css')}}" rel="stylesheet">

	<!-- Custom styles -->
	<link href="{{ url('css/style.css')}}" rel="stylesheet">
	<link href="{{ url('css/style-responsive.css')}}" rel="stylesheet">


   {{--<script src="{{ url('public/js/html5shiv.js') }}"></script>--}}
	{{--<script src="{{ url('public/js/respond.min.js') }}"></script>--}}
{{--<script src="{{ url('public/js/lte-ie7.js') }}"></script>--}}
	{{--<![endif]-->--}}

</head>
<body>
	{{--<nav class="navbar navbar-default">--}}
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<div class="collapse navbar-collapse" id="navbar">
				{{--<ul class="nav navbar-nav">--}}
					{{--<li><a href="{{ url('/') }}">Welcome</a></li>--}}
				{{--</ul>--}}

				<ul class="nav navbar-nav navbar-right">
					@if(auth()->guest())
						@if(!Request::is('auth/login'))
							<li><a href="{{ url('/auth/login') }}">Login</a></li>
						@endif
						{{--@if(!Request::is('auth/register'))--}}
							{{--<li><a href="{{ url('/auth/register') }}">Register</a></li>--}}
						{{--@endif--}}
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ auth()->user()->name }} <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="{{ url('/auth/logout') }}">Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	{{--</nav>--}}

	@yield('content')

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/resources/assets/public/js/bootstrap.min.js"></script>

	<script>
		$(document).ready(function() {
			$('.form-group input').on('focus blur', function (e) {
				$(this).parents('.form-group').toggleClass('active', (e.type === 'focus' || this.value.length > 0));
			}).trigger('blur');
		});
	</script>

</body>
</html>
