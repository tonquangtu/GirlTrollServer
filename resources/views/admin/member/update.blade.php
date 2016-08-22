@extends('admin.index')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading center_align">Cập nhật thông tin</div>
				<div class="panel-body">
					<!-- Hiển thị thông báo thành công hoặc thất bại -->
					@include('admin.alert')
					@include('admin.error')
					
					<form class="form-horizontal" role="form" method="POST" action="" id = "updateForm" enctype = "multipart/form-data">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="form-group">
							<label class="col-md-4 control-label">Username *</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="username" value="{{$user->username}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail *</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{$user->email}}" disabled>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Mật Khẩu </label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Xác Thực Mật Khẩu </label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Họ Tên *</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fullname" value="{{$user->fullname}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Giới tính *</label>
							<div class="col-md-4" style = "">
								@if($user->gender==1) 
									<input type="radio" name="gender" value = "Male" style = "margin-top:10px" checked="checked"> Nam
									<input type="radio" name ="gender" value = "Female"> Nữ
								@else
									<input type="radio" name="gender" value = "Male" style = "margin-top:10px"> Nam
									<input type="radio" name ="gender" value = "Female"  checked="checked"> Nữ
								@endif
							</div>
						</div>
		
						<div class="form-group">
							<label class="col-md-4 control-label">Avatar Hiện Tại</label>
							<div class="col-md-6">
								<img src="{!!File::exists('resources/upload/avatar/'.$user->id.'/'.$user->avatar)?url('resources/upload/avatar/'.$user->id.'/'.$user->avatar):url('resources/upload/avatar/default.jpg')!!}" alt="" style="height:100px;width:100px" class="form-control" >
							</div>
								
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Avatar</label>
							<div class="col-md-6">
								<input type="file" name="avatar"  style="margin-top: 5px; border: 1px solid #CCCCCC; width: 100%; border-radius: 5px;" value = "{{old('avatar')}}"> 
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Công Ty</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="company" value="{{ $user->company }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Địa Chỉ *</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="address" value="{{ $user->address }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Số Điện Thoại *</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="phone_number" value="{{ $user->phone_number }}">
							</div>
						</div>

						@if(Auth::user()->id != $user->id)
						<div class="form-group">
							<label class="col-md-4 control-label">Vai trò *</label>
							<div class="col-md-6" style = "">
								@if($user->role=='manager')
								<input type="radio" name="role" value = "manager" style = "margin-top:10px" checked="checked"> Manager
								<input type="radio" name ="role" value = "member"> Member
								@else
								<input type="radio" name="role" value = "manager" style = "margin-top:10px"> Manager
								<input type="radio" name ="role" value = "member"  checked="checked"> Member
								@endif
							</div>
						</div>
						@endif
						@if($user->role=='manager')
						<div class="form-group">
							<label class="col-md-4 control-label">Trạng thái *</label>
							<div class="col-md-4" style = "">
								@if($user->active==1) 
									<input type="radio" name="active" value = "active" style = "margin-top:10px" checked="checked"> Hoạt động
									<input type="radio" name ="active" value = "not_active"> Không hoạt động
								@else
									<input type="radio" name="active" value = "active" style = "margin-top:10px"> Hoạt động
									<input type="radio" name ="active" value = "not_active"  checked="checked"> Không hoạt động
								@endif
							</div>
						</div>
						@endif
						<div class="form-group">
							<div class="col-md-7 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Cập nhật
								</button>
								<input type="reset" value = "Nhập lại" class="btn btn-primary" id="reset_form">
								<button type="button" class="btn btn-primary" onclick = "window.location = '../list'">
									Quay lại
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@stop
