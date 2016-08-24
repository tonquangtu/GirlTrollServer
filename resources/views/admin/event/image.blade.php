<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Of Event</title>
	<link rel="stylesheet" type="text/css" href="{{asset('public/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('public/font-awesome/css/font-awesome.min.css')}}">

	<script src="{{asset('public/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('public/js/bootstrap.min.js')}}"></script>

	<script type="text/javascript">
		$(document).ready(function(){

			num = {{$numberAdded}};
			$('#total').text(num);
			$('input[type="checkbox"]').click(function(){
				if(this.checked){
					num++;
				}else{
					num--;
				}
				$('#total').text(num);
			});
		});
	</script>

	<style type="text/css">
		.demo{
			position: fixed;
			top: 200px;
			padding: 18px;
			margin-right:-10px;
			background-color: black;
			color: white;
			font-size: 16px;
			font-weight: bold;
		}
		.col-lg-8{
			background-color:rgb(240, 233, 234)
		}
	</style>
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-lg-2">
				<div class="demo">
					Số ảnh đã chọn: <span id = "total">0</span>
				</div>
			</div>
			<form role="form" action = "{{route('event.postAddImage',$id)}}" method = "POST">
			<input type="hidden" name="_token" value = "{{csrf_token()}}">
			<div class="col-lg-8 text-center">

				{{-- Image From Top Feed --}}
				@foreach($images as $item)
					<div class="col-lg-3 text-center">
						<a href="#cover_{{$item['imageId']}}" data-toggle="modal"><img style = "margin:15px auto; padding: 0px;" class= "col-lg-12 img-responsive" src="{{asset('').$item['urlImageThumbnail']}}"" alt=""></a>
	                    <!-- Modal -->
	                    <div id="cover_{{$item['imageId']}}" class="modal fade" role="dialog">
	                      	<div class="modal-dialog modal-md">
	                        <!-- Modal content-->
	                            <div class="modal-content">
	                              	<div class="modal-body">
	                                	<img src="{{asset('').$item['urlImageThumbnail']}}" class = "img-responsive" width="100%" style="max-height:460px">
	                              	</div>
	                              	<div class="modal-footer">
	                                	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	                              	</div>
	                            </div>
	                      	</div>
	                    </div>
						<input type="checkbox" name="images[]" value = "{{$item['imageId']}}" {{$item['checked']==1?'checked':null}}>

					</div>
				@endforeach
				<div style = "clear:both"></div>
				<input type="submit" value = "Đồng Ý" class = "btn btn-primary" style = "margin-bottom: 50px">
				<input type="button" value = "Quay Lại" class = "btn btn-primary" style = "margin-bottom: 50px" onclick = "window.location='{{route('getListEvent')}}'">
			</form>
			
			</div>
		</div>
	</div>
</body>
</html>