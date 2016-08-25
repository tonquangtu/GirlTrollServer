<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Event Edit</title>
	<link rel="stylesheet" href="{{asset('public/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('public/css/jquery-ui.min.css')}}">
	<link rel="stylesheet" href="{{asset('public/font-awesome/css/font-awesome.min.css')}}">
	 <!-- jQuery -->
    <script src="{{asset('public/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('public/js/bootstrap.min.js')}}"></script>
	<script type = "text/javascript" src = "{!!url('public/js/jquery-ui.min.js')!!}"></script>

	<script type = "text/javascript" src = "{!!url('public/js/ckeditor/ckeditor.js')!!}"></script>
	<script type = "text/javascript" src = "{!!url('public/js/ckfinder/ckfinder.js')!!}"></script>
	<script type ="text/javascript">
		var baseURL = "{!!url('/')!!}" ;
	</script>
	<script type = "text/javascript" src = "{!!url('public/js/func_ckfinder.js')!!}"></script>

	<script>
  		$( function() {
    		$( "#datepicker_start, #datepicker_end" ).datepicker({
    			showOn: "button",
			    buttonImage: "http://localhost/GirlTroll/public/image_system/calendar.png",
			    buttonImageOnly: true,
			    buttonText: "Select date",
			    changeMonth: true,
			    changeYear: true
    		});
  		} );
  	</script>

</head>
<body>
	<div class="container">
	    <h1>Edit Event</h1>
	  	<hr>
		<div class="row">
	      
		    <!-- edit form column -->
		    <div class="col-md-8 col-md-offset-2">
		        @include('admin.alert')
		        <h3>Event info</h3>
		        {{-- ['id','title','short_content','content','type','policy','active','time_start','time_end']; --}}
		        <form class="form-horizontal" role="form" action="{{route('event.update',$event->id)}}" method = "POST">
		          	<input type="hidden" name="_token" value="{{csrf_token()}}">
		          	<input type="hidden" name="_method" value="PUT">
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Title:</label>
			            <div class="col-lg-8">
			              	<input class="form-control" name="title"  type="text" value = "{{old('title',isset($event->title)?$event->title:null)}}">
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Short Content:</label>
			            <div class="col-lg-8">
			              	<textarea class="form-control" rows="6" cols="20" name="short_content">{!!old('short_content',isset($event->short_content)?$event->short_content:null)!!}</textarea>
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Content:</label>
			            <div class="col-lg-8">
			              	<textarea class="form-control" rows="6" cols="20" name="content">{!!old('content',isset($event->content)?$event->content:null)!!}</textarea>
								<script type = "text/javascript">ckeditor('content')</script>
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Type:</label>
		            	<div class="col-lg-8">
		              		<input class="form-control" name="type" type="text" value = "{!!old('type',isset($event->type)?$event->type:null)!!}">
		            	</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Policy:</label>
			            <div class="col-lg-8">
			              	<textarea class="form-control" rows="6" cols="20" name="policy">{!!old('policy',isset($event->policy)?$event->policy:null)!!}</textarea>
								<script type = "text/javascript">ckeditor('policy')</script>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<label class="col-lg-3 control-label">Active</label>
		          		<div class="col-lg-8">
		          			<select name = "active">
		          				<option value = "1" {{$event->active==1?'selected':null}}>Active</option>
		          				<option value = "0" {{$event->active==0?'selected':null}}>Deaction</option>
		          			</select>
		          		</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Time Start:</label>
		            	<div class="col-lg-4">
		              		<input class="form-control" name="time_start" type="text" id = "datepicker_start" value = "{!!old('time_start',isset($event->time_start)?date_format(date_create($event->time_start),'m/d/Y'):null)!!}">
		            	</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Time End:</label>
		            	<div class="col-lg-4">
		              		<input class="form-control" name="time_end" type="text" id = "datepicker_end"  value = "{!!old('time_end',isset($event->time_end)?date_format(date_create($event->time_end),'m/d/Y'):null)!!}">
		            	</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-md-3 control-label"></label>
		            	<div class="col-md-8">
		              		<input class="btn btn-primary" value="Save" type="submit" id = "save">
		              		<span></span>
		              		<input class="btn btn-default" value="Reset" type="reset">
		              		<span></span>
		              		<input class="btn btn-default" value="Back" type="button" onclick="window.location='{{route('getListCoverImage')}}'">
		            	</div>
		          	</div>
		        </form>
		    </div>
	  	</div>
	</div>
	<hr>
</body>
</html>