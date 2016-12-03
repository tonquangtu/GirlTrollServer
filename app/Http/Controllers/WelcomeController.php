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

	/**
	 * Get List Member
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getListMember(Request $request){
		echo 'abc';die;
		$limit = (int)$request->input('limit');
		$order = (int)$request->input('order');

		// $limit <=0: get all
		// $order = 1: order by asc of id
		// $order = 2: order by desc of id
		// $order = 3: order by desc of like
		if($limit<=0){
			switch($order){
				
				case 1:
					$members = Member::orderBy('id','DESC')->get();
					break;
				case 2:
					$members = Member::orderBy('like','DESC')->get();
					break;
				default:
					$members = Member::orderBy('id','ASC')->get();
					break;
			}
		} else{
			switch($order){
				
				case 1:
					$members = Member::orderBy('id','DESC')->take($limit)->get();
					break;
				case 2:
					$members = Member::orderBy('like','DESC')->take($limit)->get();
					break;
				default:
					$members = Member::orderBy('id','ASC')->take($limit)->get();
					break;
			}
		}
		$data = array();
		foreach($members as $item){
			$member= array();
			$member['memberId']   =$item->id;
			$member['facebookId']   =$item->facebook_id;
			$member['username']   =$item->username;
			$member['gmail']   =$item->gmail;
			$member['like']       =$item->like;
			$member['avatarUrl']  =$item->avatar_url;
			$member['totalImage'] =$item->total_image ;
			$member['active'] =$item->active ;
			$data[] = $member;
		}
		return Response::json([
			'success'=>1,
			'message'=>'Success',
			'data'=>$data
		]);
	}

	/**
	 * Update infomation for account
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public updateAccount($Request request) {

	}


}
