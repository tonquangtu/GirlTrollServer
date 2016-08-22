<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Event Add</title>
	<link rel="stylesheet" href="{{asset('public/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('public/font-awesome/css/font-awesome.min.css')}}">
	 <!-- jQuery -->
    <script src="{{asset('public/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('public/js/bootstrap.min.js')}}"></script>
</head>
<body>
	<div class="container">
	    <h1>Add Event</h1>
	  	<hr>
		<div class="row">
	      
		    <!-- edit form column -->
		    <div class="col-md-8 col-md-offset-2">
		        @include('admin.alert')
		        <h3>Event info</h3>
		        {{-- ['id','title','short_content','content','type','policy','active','time_start','time_end']; --}}
		        <form class="form-horizontal" role="form" action="{{route('event.store')}}" method = "POST" enctype="multipart/form-data">
		          	<input type="hidden" name="_token" value="{{csrf_token()}}">
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Title:</label>
			            <div class="col-lg-8">
			              	<input class="form-control" name="title"  type="text">
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Short Content:</label>
			            <div class="col-lg-8">
			              	<textarea></textarea>
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Title:</label>
			            <div class="col-lg-8">
			              	<input class="form-control" name="title"  type="text">
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Image:</label>
		            	<div class="col-lg-8">
		              		<input class="" name="image_new" type="file">
		            	</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Number Cover:</label>
		            	<div class="col-lg-8">
		              		<input class="form-control" name = "number_cover" value="0" type="text" disabled>
		            	</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-md-3 control-label"></label>
		            	<div class="col-md-8">
		              		<input class="btn btn-primary" value="Save" type="submit">
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