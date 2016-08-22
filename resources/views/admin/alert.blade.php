@if(Session::has('flash_message'))
	<div class = "alert alert-{!!Session::get('flash_level')!!} fade in">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		{!!Session::get('flash_message')!!}
	</div>
@endif