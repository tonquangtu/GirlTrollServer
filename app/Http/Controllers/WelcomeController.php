<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Feed, App\Member;
use Response;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		//C1: Lấy từ web service
		// $getdata = http_build_query(
		//     array(
		//         'currentFeedId' => '-1',
		//         'limit' => '5'
		//     )
		// );

		// $result = file_get_contents(URLWEB.'feed/new?'.$getdata, false);
		
		// $data=json_decode($result,true);
		// if(!isset($data['success'])){
		// 	echo "Not has feed";
		// }else if($data['success']==0){
		// 	echo $data['message'];
		// }else{
		// 	$feeds= $data['data'];
		// }

		//C2: Lấy từ Database (dùng khi làm trực tiếp trên project của web)
		// $feeds = Feed::all()->toArray();
		// return view('customer.index',compact('feeds'));
	}

	public function getListMember(Request $request){
		$limit = (int)$request->input('limit');
		if($limit!=0){
			$members = Member::orderBy('like','DESC')->take($limit)->get();
		}else{
			$members = Member::all();
		}
		$data = array();
		foreach($members as $item){
			$member= array();
			$member['id']         =$item->id;
			$member['memberId']   =$item->member_id;
			$member['username']   =$item->username;
			$member['rank']       =$item->rank;
			$member['like']       =$item->like;
			$member['avatarUrl']  =$item->avatar_url;
			$member['totalImage'] =$item->total_image ;
			$data[] = $member;
		}
		return Response::json([
			'success'=>1,
			'message'=>'Success',
			'data'=>$data
		]);
	}

}
