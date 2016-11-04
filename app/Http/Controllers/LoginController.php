<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Member;
use Response;
use Hash;
use Input,Mail;

class LoginController extends Controller {
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$member = Member::where('facebook_id','=',$request->input('facebookId'))->first();
		if(isset($member->id)){
			$member->username = $request->input('username');
			$member->avatar_url = $request->input('avatarUrl');
			$member->gmail = $request->input('gmail');
			$member->save();
			$success = 0;
		}else {
			$member = new Member;
			$member->facebook_id = $request->input('facebookId');
			$member->username = $request->input('username');
			$member->gmail = $request->input('gmail');
			$member->password ='';
			$member->like = 0;
			$member->avatar_url = $request->input('avatarUrl');
			$member->total_image = 0;
			$member->active=1;
			$member->save();
			$success = 1;
		}
		$data = [
			'memberId'=>$member->id,
			'like'=>$member->like,
			'totalImage' => $member->total_image	
		];
		$send = [
			'success'=>$success,
			'message'=>($success==1)?"Success":"Thành viên đã tồn tại",
			'data' => $data
		];

		return Response::json($send);
	}

	/**
	 * Sign up a acount
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function signup(Request $request){
		// Get data from client
		$gmail = $request->input('gmail');
		$username = $request->input('username');
		$password = $request->input('password');

		$temp = Member::where('gmail',$gmail)->first();
		if(isset($temp->id) && !empty($temp->password)){
			return Response::json([
					'success' => 0,
					'message' =>'Tài khoản đã tồn tại',
					'data' => null
				]);
		}
		// Save account
		$member = new Member;
		$member->facebook_id = '';
		$member->username = $username;
		$member->gmail = $gmail;
		$member->password = Hash::make($password);
		$member->like=0;
		$member->avatar_url = 'public/avatar/default.jpg';
		$member->total_image = 0;
		$member->active = 0;
		$member->save();

		$data = [
			'hoten'=>$username,
			'gmail'=>$gmail,
			'password'=>$password
		];
		try{
			Mail::send('emails.active',$data,function($msg){
				$msg->from('tungnt.tvg01.hust@gmail.com','GirlTrollSV');
				$msg->to(Input::get('gmail'),Input::get('username'))->subject('Xác thực người dùng');
			});
		}catch(Exception $e){
			$member->delete();
			return Response::json([
				'success'=>0,
				'message'=>'Gửi Mail Kích Hoạt Không Thành Công',
				'data'=>null
				]);
		}
		
		return Response::json([
				'success'=>1,
				'message'=>'Success',
				'data'=> [
					'memberId' => $member->id,
					'like' => $member->like,
					'totalImage' =>$member->total_image
				]
			]);

	}

	public function loginNormal(Request $request){
		//Get data from client
		$gmail = $request->input('gmail');
		$password = $request->input('password');

		$member = Member::where('gmail',$gmail)->first();
		if(isset($member->id)){

			if(crypt($password, $member->password)==$member->password){
				if($member->active==0){
					return Response::json([
						'success'=>0,
						'message'=>'Chưa Kích Hoạt Tài Khoản! Hãy vào mail để kích hoạt tài khoản',
						'data'=>[
									'memberId'=>$member->id,
									'username'=>$member->username,
									'avatarUrl'=>URLWEB.$member->avatar_url,
									'like'=>$member->like,
									'totalImage'=>$member->total_image,
									'active'=>$member->active
								]
						]);
				}
				
				return Response::json([
							'success'=>1,
							'message'=>'Success',
							'data'=>[
								'memberId'=>$member->id,
								'username'=>$member->username,
								'avatarUrl'=>URLWEB.$member->avatar_url,
								'like'=>$member->like,
								'totalImage'=>$member->total_image,
								'active'=>$member->active
							]
						]);	
			}

		}

		return Response::json([
			'success'=>0,
			'message'=>'Tài khoản hoặc mật khẩu không đúng',
			'data'=>null
			]);
	}

}
