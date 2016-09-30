<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Member;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function postLogin(LoginRequest $request){
		$data = array(
			'email'=>$request->email,
			'password'=>$request->password,
			'active'=>'1',
		);
		if($this->auth->attempt($data,$request->has('remember'))){
			return redirect('home');
		} else {
			return redirect('auth/login')->with(['flash_level'=>'danger','flash_message'=>'Tài khoản hoặc mật khẩu không đúng!']);
		}
	}


	/**
	 * Active account
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function active(Request $request){
		$gmail = $request->input('gmail');
		$password = $request->input('password');
		$member = Member::where('gmail',$gmail)->first();
		if(isset($member->id)){
			if(crypt($password, $member->password)==$member->password){
				$member->active = 1;
				$member->save();
				return view('success');

			}
		}
	}

}
