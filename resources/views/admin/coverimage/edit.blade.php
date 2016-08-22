<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Image Cover Edit</title>
	<link rel="stylesheet" href="{{asset('public/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('public/font-awesome/css/font-awesome.min.css')}}">
	 <!-- jQuery -->
    <script src="{{asset('public/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('public/js/bootstrap.min.js')}}"></script>
</head>
<body>
	<div class="container">
	    <h1>Edit Image Cover</h1>
	  	<hr>
		<div class="row">
	      
		    <!-- edit form column -->
		    <div class="col-md-8 col-md-offset-2">
		        @include('admin.alert')
		        <h3>Image Cover info</h3>
		        
		        <form class="form-horizontal" role="form" action="{{route('coverimage.update',$coverimage->id)}}" method = "POST" enctype="multipart/form-data">
		          	<input type="hidden" name="_token" value="{{csrf_token()}}">
		          	<input type="hidden" name="_method" value = "PUT">
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Title:</label>
			            <div class="col-lg-8">
			              	<input class="form-control" name="title" value="{{old('title',isset($coverimage->title)?$coverimage->title:null)}}" type="text">
			            </div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Image Current:</label>
		            	<div class="col-lg-8">
		              		<a href="#cover_{{$coverimage->id}}" data-toggle="modal"><img src="{{asset('').$coverimage->url_image_thumbnail}}" alt="{{$coverimage->title}}"></a>
                            <!-- Modal -->
                            <div id="cover_{{$coverimage->id}}" class="modal fade" role="dialog">
                              	<div class="modal-dialog modal-lg">
	                                <!-- Modal content-->
	                                <div class="modal-content">
	                                    <div class="modal-header">{{$coverimage->title}}</div>
	                                  	<div class="modal-body">
	                                    	<img src="{{asset('').$coverimage->url_image}}" class = "img-responsive" width="100%" style="max-height:460px">
	                                  	</div>
	                                  	<div class="modal-footer">
	                                    	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	                                  	</div>
	                                </div>
                              	</div>
                            </div>
		            	</div>
		          	</div>
		          	{{-- <div class="form-group">
		            	<label class="col-lg-3 control-label">Image:</label>
		            	<div class="col-lg-8">
		              		<input class="" name="image_new" type="file">
		            	</div>
		          	</div> --}}
		          	<div class="form-group">
		            	<label class="col-lg-3 control-label">Number Cover:</label>
		            	<div class="col-lg-8">
		              		<input class="form-control" name = "number_cover" value="{{old('number_cover',isset($coverimage->number_cover)?$coverimage->number_cover:null)}}" type="text" disabled>
		            	</div>
		          	</div>
		          	<div class="form-group">
		            	<label class="col-md-3 control-label"></label>
		            	<div class="col-md-8">
		              		<input class="btn btn-primary" value="Save Changes" type="submit">
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