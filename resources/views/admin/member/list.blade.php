@extends('admin.master')

@section('content')
<script type="text/javascript">
    $(document).ready(function(){
        $("#dashboard").removeClass('active');
        $("#member").addClass('active');
    });
</script>
	<!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Member
            </h1>
            <ol class="breadcrumb">
                <li>
                    <i class="fa fa-dashboard"></i>  <a href="{{url('/')}}">Dashboard</a>
                </li>
                <li class="active">
                    <i class="fa fa-table"></i> Member
                </li>
            </ol>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                {{-- ['id','member_id', 'username','rank','like','avatar_url','total_image'] --}}
                    <thead>
                        <tr class="bg-primary">
                            <th class = "text-center">Id</th>
                            <th class = "text-center">Member Id</th>
                            <th class = "text-center">Username</th>
                            <th class = "text-center">Rank</th>
                            <th class = "text-center">Like</th>
                            <th class = "text-center">Avatar Url</th>
                            <th class = "text-center">Total Image</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@foreach($members as $member)
                        <tr>
                            <td>{{$member->id}}</td>
                            <td>{{$member->member_id}}</td>
                            <td>{{$member->username}}</td>
                            <td>{{$member->rank}}</td>
                            <td>{{$member->like}}</td>
                            <td>{{$member->avatar_url}}</td>
                            <td>{{$member->total_image}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {!!$members->render()!!}
            </div>
        </div>
    </div>
    <!-- /.row -->
@stop