<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Feed, App\Member;
use Response, Mail, Input;

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
		$limit = (int)$request->input('limit');
		$order = (int)$request->input('order');
		$currentMemberId = (int)$request->input('currentMemberId');

		// $limit <=0: get all
		// $order = 1: order by desc of id
		// $order = 2: order by desc of like
		// default: order by asc of id
		if($limit<=0){
			switch($order){
				
				case 1:
					if($currentMemberId==-1)
						$members = Member::orderBy('id','DESC')->get();
					else
						$members = Member::where('id','<',$currentMemberId)->orderBy('id','DESC')->get();
					break;
				case 2:
					$members = Member::orderBy('like','DESC')->get();
					break;
				default:
					$members = Member::where('id','>',$currentMemberId)->orderBy('id','ASC')->get();
					break;
			}
		} else{
			switch($order){
				
				case 1:
					if($currentMemberId==-1)
						$members = Member::orderBy('id','DESC')->take($limit)->get();
					else
						$members = Member::where('id','<',$currentMemberId)->orderBy('id','DESC')->take($limit)->get();
					break;
				case 2:
					$members = Member::orderBy('like','DESC')->take($limit)->get();
					break;
				default:
					$members = Member::where('id','>',$currentMemberId)->orderBy('id','ASC')->take($limit)->get();
					break;
			}
		}
		$data = array();
		if(count($members)==0){
			$data=null;
			$message = 'Đã hiển thị hết các thành viên';
			$success = 0;
		}else{
			foreach($members as $item){
				$member= array();
				$member['memberId']   =$item->id;
				$member['facebookId']   =$item->facebook_id;
				$member['username']   =$item->username;
				$member['gmail']   =$item->gmail;
				$member['like']       =$item->like;
				$member['avatarUrl']  =$item->facebook_id==''?URLWEB.$item->avatar_url:$item->avatar_url;
				$member['totalImage'] =$item->total_image ;
				$member['active'] =$item->active ;
				$data[] = $member;
			}
			$success = 1;
			$message = 'Success';
		}
		
		return Response::json([
			'success'=>$success,
			'message'=>$message,
			'data'=>$data,
			'paging'=>['before'=>$currentMemberId, 'after'=>isset($members->last()->id)?$members->last()->id:0]
		]);
	}

	public function postContact(Request $request){
		$data=[
			'hoten' => $request->input('name'),
			'email' => $request->input('email'),
			'mess'  => $request->input('mess')
		];
		Mail::send('emails.reply',$data,function($msg){
			$msg->from('girltrollsv@gmail.com',Input::get('name'));
			$msg->to('girltrollsv@gmail.com')->subject('GirlTrollSV Phản hồi của khách hàng');
		});
	}

	/**
	 * Get information member
	 * @return [type] [description]
	 */
	public function getMember(Request $request, $id){
		$item = Member::find($id);

		if(isset($item->id)){
			$member['memberId']   =$item->id;
			$member['facebookId']   =$item->facebook_id;
			$member['username']   =$item->username;
			$member['gmail']   =$item->gmail;
			$member['like']       =$item->like;
			$member['avatarUrl']  =$item->facebook_id==''?URLWEB.$item->avatar_url:$item->avatar_url;
			$member['totalImage'] =$item->total_image ;
			$member['active'] =$item->active ;
			
			return Response::json(['success'=>1,'message'=>'Success','data'=>$member]);
		}else{

		}
	}

}
