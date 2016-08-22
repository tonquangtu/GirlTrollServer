@extends('admin.master')

@section('content')
<script type="text/javascript">
    $(document).ready(function(){
        $("#dashboard").removeClass('active');
        $("#imagecover").addClass('active');
    });
</script>
	<!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Image Cover
            </h1>
            <ol class="breadcrumb">
                <li>
                    <i class="fa fa-dashboard"></i>  <a href="{{url('/')}}">Dashboard</a>
                </li>
                <li class="active">
                    <i class="fa fa-image"></i> Image Cover
                </li>
            </ol>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-offset-10">
            <a href="{{route('coverimage.create')}}"><button type = "button" class ="btn btn-success">Add New</button></a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            @include('admin.alert')
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    {{-- ['id','title','url_image','url_image_thumbnail', 'number_cover'] --}}
                    <thead>
                        <tr class="bg-primary">
                            <th class = "text-center">Id</th>
                            <th class = "text-center">Title</th>
                            <th class = "text-center">Image</th>
                            <th class = "text-center">Url Image</th>
                            <th class = "text-center">Url Image Thumbnail</th>
                            <th class = "text-center">Number Cover</th>
                            <th class = "text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coverimages as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->title}}</td>
                            <td class = "text-center"><a href="#cover_{{$item->id}}" data-toggle="modal"><img src="{{asset('').$item->url_image_thumbnail}}" alt="{{$item->title}}"></a>
                                <!-- Modal -->
                                <div id="cover_{{$item->id}}" class="modal fade" role="dialog">
                                  <div class="modal-dialog modal-lg">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">{{$item->title}}</div>
                                      <div class="modal-body">
                                        <img src="{{asset('').$item->url_image}}" class = "img-responsive" width="100%" style="max-height:460px">
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                            <td>{{$item->url_image}}</td>
                            <td>{{$item->url_image_thumbnail}}</td>
                            <td>{!!number_format($item->number_cover,0,',','.')!!}</td>
                            <td>
                                <a href="{{route('coverimage.edit',$item->id)}}" class = "btn btn-link">Edit</a>
                                <form action = "{{route('coverimage.destroy',$item->id)}}" method = "POST" role="form">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="_method" value = "DELETE">
                                    <button type = "submit" class="btn btn-link" onclick = "window.confirm('Do you sure delete this image')">Delete</a>    
                                </form>
                            </td>
                        </tr>
                                
                        @endforeach
                    </tbody>
                </table>
                {!!$coverimages->render()!!}
                {{-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button> --}}

            </div>
        </div>
    </div>
    <!-- /.row -->
@stop