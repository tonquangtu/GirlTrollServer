<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Member;
use Response;

class LoginController extends Controller {
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$member = Member::where('member_id','=',$request->input('memberId'))->first();
		if(isset($member->id)){
			$success = 0;
		}else {
			$member = new Member;
			$member->member_id = $request->input('memberId');
			$member->username = $request->input('username');
			$member->rank = 0;
			$member->like = 0;
			$member->avatar_url = $request->input('avatarUrl');
			$member->total_image = 0;
			$member->save();
			$success = 1;
		}
		$data = [
			'rank'=>$member->rank,
			'like'=>$member->like,
			'totalImage' => $member->total_image	
		];
		$send = [
			'success'=>$success,
			'message'=>($success==1)?"Success":"Member was existed",
			'data' => $data
		];

		return Response::json($send);
	}

}
