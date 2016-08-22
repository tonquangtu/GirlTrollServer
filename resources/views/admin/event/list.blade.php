@extends('admin.master')

@section('content')
<script type="text/javascript">
    $(document).ready(function(){
        $("#dashboard").removeClass('active');
        $("#event").addClass('active');
    });
</script>
	<!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Event
            </h1>
            <ol class="breadcrumb">
                <li>
                    <i class="fa fa-dashboard"></i>  <a href="{{url('/')}}">Dashboard</a>
                </li>
                <li class="active">
                    <i class="fa fa-image"></i> Event
                </li>
            </ol>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-offset-10">
            <a href="{{route('event.create')}}"><button type = "button" class ="btn btn-success">Add New</button></a>
        </div>
    </div>
    <br>
    
    <div class="row">
        <div class="col-lg-12">
            @include('admin.alert')
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                {{-- ['id','title','short_content','content','type','policy','active','time_start','time_end'] --}}
                    <thead>
                        <tr class="bg-primary">
                            <th class = "text-center">Id</th>
                            <th class = "text-center">Title</th>
                            <th class = "text-center">Short Content</th>
                            <th class = "text-center">Content</th>
                            <th class = "text-center">Type</th>
                            <th class = "text-center">Policy</th>
                            <th class = "text-center">Active</th>
                            <th class = "text-center">Time Start</th>
                            <th class = "text-center">Time End</th>
                            <th class = "text-center">Action</th>
                            <th class = "text-center">Add Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->title}}</td>
                            <td>{!!$item->short_content!!}</td>
                            <td class = "text-center"><button type="button" data-toggle="modal" data-target="#content_{{$item->id}}">View Detail</button>
                                <!-- Modal -->
                                <div id="content_{{$item->id}}" class="modal fade" role="dialog">
                                  <div class="modal-dialog modal-lg">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                    <div class="modal-header">{{$item->title}}</div>
                                      <div class="modal-body">
                                        <p>{!!$item->content!!}</p>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                            <td>{{$item->type}}</td>
                            <td class = "text-center"><button type="button" data-toggle="modal" data-target="#policy_{{$item->id}}">View Detail</button>
                                <!-- Modal -->
                                <div id="policy_{{$item->id}}" class="modal fade" role="dialog">
                                  <div class="modal-dialog modal-lg">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                    <div class="modal-header">Policy</div>
                                      <div class="modal-body">
                                        <p>{!!$item->policy!!}</p>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                            <td>{{($item->active==1)?'Active':'Deactive'}}</td>
                            <td>{{$item->time_start}}</td>
                            <td>{{$item->time_end}}</td>
                            <td>
                                <a href="{{route('event.edit',$item->id)}}" class = "btn btn-link">Edit</a>
                                <form action = "{{route('event.destroy',$item->id)}}" method = "POST" role="form">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="_method" value = "DELETE">
                                    <button type = "submit" class="btn btn-link" onclick = "window.confirm('Do you sure delete this event')">Delete</a>    
                                </form>
                            </td>
                            <td><a href="{{route('event.addImage',$item->id)}}">Add</a></td>
                        </tr>
                                
                        @endforeach
                    </tbody>
                </table>
                {!!$events->render()!!}
                {{-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button> --}}

            </div>
        </div>
    </div>
    <!-- /.row -->
@stop