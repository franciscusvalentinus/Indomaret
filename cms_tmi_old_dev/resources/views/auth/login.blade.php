@extends('app')
@section('content')

{{--<body class="login-img3-body">--}}
<style>
	*{
		margin: 0;
		padding: 0;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
		box-sizing:border-box;
	}
	body {
		background:#f4f4f4 ;
		font-family: 'open sans', sans-serif;
	}
	.form-box{
		background: #fff;
		margin: 30px auto;
		max-width: 500px;
		box-shadow: 0 3px 6px 0px rgba(0,0,0,0.16), 0 3px 6px 0px rgba(0,0,0,0.23);
	}
	form#login-form {
		overflow: hidden;
		position: relative;
		padding: 40px;
	}
	.head {
		color: #fff;
		font-size: 34px;
		font-weight: normal;
		padding: 50px 0;
		text-align: center;
		text-transform: uppercase;
		background: #6498fe;
	}
	.form-group {
		margin-bottom: 15px;
		position: relative;
		width: 100%;
		overflow: hidden;
	}

	.form-group .label-control {
		color: #888;
		display: block;
		font-size: 14px;
		position: absolute;
		top: 0;
		left: 0;
		padding: 0;
		width: 100%;
		pointer-events: none;
		height: 100%;
	}
	.form-group .label-control::before,
	.form-group .label-control::after{
		content: "";
		left: 0;
		position: absolute;
		bottom: 0;
		width: 100%;
	}
	.form-group .label-control::before{
		border-bottom: 1px solid #B9C1CA;
		transition: transform 0.3s;
		-webkit-transition: -webkit-transform 0.3s;
	}

	.form-group .label-control::after {
		border-bottom: 2px solid #03A9F4;
		-webkit-transform: translate3d(-100%, 0, 0);
		transform: translate3d(-100%, 0, 0);
		-webkit-transition: -webkit-transform 0.3s;
		transition: transform 0.3s;
	}

	.form-control {
		border: none;
		border-radius: 0;
		margin-top: 20px;
		padding: 12px 0;
		width: 100%;
		font-size: 14px;
	}
	.form-control:focus {
		outline: none;
		box-shadow: none;
	}

	.form-group .label-control .label-text{
		-webkit-transform: translate3d(0, 30px, 0) scale(1);
		-moz-transform: translate3d(0, 30px, 0) scale(1);
		transform: translate3d(0, 30px, 0) scale(1);
		-webkit-transform-origin: left top;
		-moz-transform-origin: left top;
		transform-origin: left top;
		-webkit-transition: 0.3s;
		-moz-transition: 0.3s;
		transition: 0.3s;
		position: absolute;
	}
	.active .label-control::after{
		-webkit-transform: translate3d(0%, 0, 0);
		transform: translate3d(0%, 0, 0);
	}
	.active .label-control .label-text {
		opacity: 1;
		-webkit-transform: scale(0.9);
		-moz-transform: scale(0.9);
		transform: scale(0.9);
		color: #03A9F4 !important;
	}

	.input-field label:before{
		content: '';
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		border-bottom: 1px solid #B9C1CA;
		transition: transform 0.3s;
		-webkit-transition: -webkit-transform 0.3s;
	}


	input.btn[type="submit"] {
		background: #6498fe;
		border:none;
		border-radius: 2px;
		color: #fff;
		cursor: pointer;
		font-size: 16px;
		font-weight: bold;
		letter-spacing: 3px;
		margin: 5px 0;
		outline: medium none;
		overflow: hidden;
		padding: 10px;
		text-transform: uppercase;
		transition: all 0.15s ease-in-out 0s;
		width: 100%;
		box-shadow: 0 1px 2px 0px rgba(0,0,0,0.16), 0 1px 2px 0px rgba(0,0,0,0.23);
	}
	input.btn[type="submit"]:hover {
		background: #4b81eb;
		box-shadow: 0 2px 4px 0px rgba(0,0,0,0.16), 0 2px 4px 0px rgba(0,0,0,0.23);
	}
	.text-p{
		font-size: 14px;
		text-align: center;
		margin: 10px 0;
	}
	.text-p a{
		color: #175690;
	}
	@media (max-width: 480px){
		form#login-form {
			width: 90%;
			margin: 30px auto;
		}
	}

</style>
{{--<div class="container">--}}
	<div class="form-box">
		<div class="head" style="
			 padding-left: 30px;
			 padding-right: 30px;">
			CMS Toko Mitra IGR
			{{--<a class="logo" style="margin-bottom: 30px;">Program<span>&nbsp; CMS</span> <span class="lite">TMI</span></a>--}}
		</div>
		<form id="login-form" role="form" method="POST" action="{{ url('/getlogin') }}">
			@if (session('err'))
				<div class="col-md-12 igr-flat" style="margin-top:10px;margin-top:10px;padding-left: 0px;padding-right: 0px;">
					<div class="alert alert-danger igr-flat">
						<strong>{{session('err')}}</strong>
					</div>
				</div>
			@endif
			<div class="form-group">
				<label class="label-control">
					<span class="label-text">Email</span>
				</label>
				<input type="email" name="email" class="form-control" />
				{{--<span class="input-group-addon"><i class="icon_profile"></i></span>--}}
				{{--<input type="text" name="email" class="form-control" placeholder="Username" autofocus>--}}
			</div>
			<div class="form-group">
				<label class="label-control">
					<span class="label-text">Password</span>
				</label>
				<input type="password" name="password" class="form-control" />
			</div>
			<input type="submit" value="Login" class="btn" />
			{{--<p class="text-p">Don't have an account? <a href="#">Sign up</a> </p>--}}
		</form>
	</div>
	{{--<form class="login-form" role="form" method="POST" action="{{ url('/getlogin') }}">--}}
		{{--@if (session('err'))--}}
			{{--<div class="col-md-12 igr-flat" style="margin-top:10px;">--}}
				{{--<div class="alert alert-danger igr-flat">--}}
					{{--<strong>{{session('err')}}</strong>--}}
				{{--</div>--}}
			{{--</div>--}}
		{{--@endif--}}
		{{--<div class="login-wrap" style="padding-left: 50px!important;padding-right: 50px;!important;">--}}
			{{--<p class="login-img"><i class="icon_lock_alt"></i></p>--}}
			{{--<a class="logo" style="margin-bottom: 30px;">Program<span>&nbsp; CMS</span> <span class="lite">TMI</span></a>--}}
			{{--<div class="input-group">--}}
				{{--<span class="input-group-addon"><i class="icon_profile"></i></span>--}}
				{{--<input type="text" name="email" class="form-control" placeholder="Username" autofocus>--}}
			{{--</div>--}}
			{{--<div class="input-group">--}}
				{{--<span class="input-group-addon"><i class="icon_key_alt"></i></span>--}}
				{{--<input type="password" class="form-control" placeholder="Password" name="password">--}}
			{{--</div>--}}
			{{--<label class="checkbox">--}}
				{{--<input type="checkbox" value="remember-me"> Remember me--}}
				{{--<span class="pull-right"> <a href="#"> Forgot Password?</a></span>--}}
			{{--</label>--}}
			{{--<button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>--}}
			{{--<button class="btn btn-info btn-lg btn-block" type="submit">Signup</button>--}}
		{{--</div>--}}
	{{--</form>--}}
{{--</div>--}}

{{--</body>--}}
@endsection
