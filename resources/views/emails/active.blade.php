Cảm ơn bạn {{$hoten}} đã đăng kí trở thành thành viên của GirlTrollSV<br/>
Bạn vui lòng click nút Kích Hoạt để hoàn thành đăng kí và đăng nhập tài khoản
<form action = "{{URLWEB.'/active'}}" method = "GET">
	<input type="hidden" name="gmail" value = "{{$gmail}}">
	<input type="hidden" name="password" value="{{$password}}">
	<input type="submit" name="submit" value = "Kích Hoạt">
</form> 