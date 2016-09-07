<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login Admin</title>

	<link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/login.css') }}" rel="stylesheet">
	<script type="text/javascript" src="{{ asset('public/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{ asset('public/js/bootstrap.min.js')}}"></script>
	<script type="text/javascript" src="{{ asset('public/js/my_script.js')}}"></script>
	
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Login</div>
					<div class="panel-body">
						@if (count($errors) > 0)
							<div class="alert alert-danger">
								<strong>Lỗi!</strong> Xảy ra lỗi với dữ liệu nhập vào<br><br>
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							@if(Session::has('flash_level'))
								<div class="alert alert-{{Session::get('flash_level')}}">
									{{Session::get('flash_message')}}
								</div>
							@endif
							<div class="form-group">
								<label class="col-md-4 control-label">E-Mail Address</label>
								<div class="col-md-6">
									<input type="email" class="form-control" name="email" value="{{ old('email') }}">
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label">Password</label>
								<div class="col-md-6">
									<input type="password" class="form-control" name="password">
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="remember" checked> Remember Me
										</label>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary">Login</button>

									{{-- <a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a> --}}
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
