<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Feed;
use Response;
use Image;
use App\Image as ImageModel;
use App\Video, App\Member, App\MemberLikeFeed;
use DB;
use Thumbnail;

class FeedController extends Controller {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	
	public function store(Request $request) {
		// Get data from client
		$memberId = $request->input('memberId');
		$title = $request->input('title');
		$school = $request->input('school');
		$totalFile = (int)$request->input('totalFile');

		// Check member exist
		$member = Member::find($memberId);
		if(!isset($member)){
			return Response::json([
					'success' => 0,
					'message' => 'Thành viên không tồn tại'
				]);
		}

		//Add Feed
		$feed = new Feed;
		$feed->title = $title;
		$feed->school = $school;
		$feed->time = date('Y-m-d H:i:s');
		$feed->like = 0;
		$feed->comment = 0;
		// $feed->share = 0;
		$feed->vote = 0;
		$feed->member_id = $memberId;
		$feed->save();

		//Add image or video
		if($totalFile!=0){
			$linkFace = $request->input('linkFace');
			$typeImage = $request->input('type');

			$width = array();
			$height = array();
			$pathImg = 'public/image';
			$pathThumb = 'public/image/thumbnail';

			//Upload feed image
			for($i=0; $i<$totalFile; $i++){
				if($request->hasFile('file_'.$i)){
					$files[] = $request->file('file_'.$i);
				}
			}
			//Width and height of thumbnail change when number file change
			switch(count($files)){
				case 1: $width[0]=500; $height[0]=500;
						break;
				case 2: $width[0]=$width[1]=250; $height[0]=$height[1]=500;
						break;
				case 3: $width[0]=500; $width[1]=$width[2]=250; $height[0]=$height[1]=$height[2]=250;
						break;
				case 4: $width[0]=$width[1]=$width[2]=$width[3]=250; $height[0]=$height[1]=$height[2]=$height[3]=250;
						break;
				default: break;
			}

			//Save file
			$i=0;
			foreach($files as $image){
				$imagename = changeTitle(time().$image->getClientOriginalName());
				$this->resizeImagePost($image, $imagename, $width[$i], $height[$i], $pathImg, $pathThumb);

				$img = new ImageModel;	
				$img->url_image = $pathImg.'/'.$imagename;
				$img->type = $typeImage;
				$img->link_face = $linkFace;
				$img->feed_id = $feed->id;
				$img->url_image_thumbnail = $pathThumb.'/'.$imagename;
				$img->save();
				$i++;
			}

			// Add number image to member
			$member->first()->total_image += $i;
			$member->first()->save();

			if($i!=$totalFile){
				$success = 0;
				$message = "Thêm Không Đủ Hình Ảnh";
			} else {
				$success = 1;
				$message = "Success";
			}

		} else{

			//Upload feed video
			$typeVideo = $request->input('type');

			//0: file, 1: youtube
			if($typeVideo == 1){
				$vde = new Video;
				$vde->url_video = $request->input('youtube');
				$vde->url_image_thumbnail = '';
				$vde->type = $typeVideo;
				$vde->feed_id = $feed->id;
				$vde->save();

				$success = 1;
				$message = "Success";
			} else{
				if($request->hasFile('file')){

					//save file on system
					$video = $request->file('file');
					$videoname = changeTitle(time().$video->getClientOriginalName());
					$pathVideo = 'public/video';
					$pathThumb = 'public/video/thumbnail';
					$video->move($pathVideo,$videoname);

					if($request->hasFile('thumbnailVideo')){
						$thumbnail = $request->file('thumbnailVideo');
						$thumbnailname = changeTitle(time().$thumbnail->getClientOriginalName());
						$thumbnail->move($pathThumb,$thumbnailname);
					} else{
						$thumbnailname = 'defaultThumbnail.jpg';
					}

					//save file on database
					$vde = new Video;
					$vde->url_video = $pathVideo.'/'.$videoname;
					$vde->type = $typeVideo;
					$vde->url_image_thumbnail = $pathThumb.'/'.$thumbnailname;
					$vde->feed_id = $feed->id;
					$vde->save();

					$success = 1;
					$message = "Success";
				} else{
					$success = 0;
					$message = "Video Không Tìm Thấy";
				}
			} 
			
		}

		return Response::json([
			'success'=>$success,
			'message'=>$message
		]);
	}


	/**
	 * Store Image And Store Thumbnail
	 * @param  file   $image     
	 * @param  string $imagename 
	 * @param  int    $width     
	 * @param  int    $height    
	 * @param  string $pathImg   
	 * @param  string $pathThumb 
	 * @return null            
	 */
	public function resizeImagePost($image, $imagename, $width, $height, $pathImg, $pathThumb){

		//Move image to the pathImg
		$image->move($pathImg, $imagename);
		
		//Create thumbnail and save to the pathThumbnail use library Image Laravel
		$img = Image::make($pathImg.'/'.$imagename);
		$img->resize($width, $height)->save($pathThumb.'/'.$imagename);

		return null;
	}


	/**
	 * Get List New Feed
	 * 
	 * @return Response
	 */
	public function getNewFeed(Request $request){
		// Get data from client
		$memberId = $request->input('memberId');
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');

		// //If member not login
		// if($memberId==''){
		// 	$idMember = '';		
		// }else{
		// 	$member = Member::where('id',$memberId)->first();
		// 	if(isset($member->id)){
		// 		$idMember = $member->id;
		// 	}else{
		// 		$idMember = '';
		// 	}
		// }

		//Error if end of feed
		if($currentFeedId==Feed::orderBy('id','ASC')->first()->id){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentFeedId == -1 => the first load new feed
			if($currentFeedId == -1){
				$current = Feed::orderBy('id','ASC')->get()->last()->id+1;
			} else{
				$current = $currentFeedId;
			}

			// $feeds = Feed::where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$feeds = Feed::where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$data = $this->getListFeed($feeds, $memberId);

			$success = 1;
			$afterFeedId = (int)$feeds->last()->id;
		}
		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Feed':'Success',
			'data'    => $data,
			'paging'  => [
				'before' => $currentFeedId,
				'after'  => $afterFeedId
			]
		];
		return Response::json($send);
		
	}

	public function getFeedRefresh(Request $request){
		// Get data from client
		$memberId = $request->input('memberId');
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');


		//If member not login
		// if($memberId==''){
		// 	$idMember = '';		
		// }else{
		// 	$member = Member::where('member_id',$memberId)->first();
		// 	if(isset($member->id)){
		// 		$idMember = $member->id;
		// 	}else{
		// 		$idMember = '';
		// 	}
		// }
		//currentFeedId == -1 => the first load new feed
		//currentFeedId == max feed => this is newest feed
		//else load $limit new feed
		if($currentFeedId == -1){
			// $feeds = Feed::where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$feeds = Feed::orderBy('id','DESC')->take($limit)->get();
			
			$data = $this->getListFeed($feeds, $memberId);

			$success = 1;
			$afterFeedId = (int)$feeds->first()->id;
			$message = "Success";
		}else if($currentFeedId==Feed::order('id','ASC')->last()->id){
			$data = null;
			$success = 1;
			$afterFeedId = $currentFeedId;
			$message = "Feed Mới Nhất";
		} else {
			$feeds = Feed::where('id','>',$currentFeedId)->orderBy('id','ASC')->take($limit)->get();
			//Sort By DESC OF ID
			$feeds->sortByDesc('id', $options = SORT_REGULAR);

			$data = $this->getListFeed($feeds, $memberId);

			$success = 1;
			$afterFeedId = (int)$feeds->first()->id;
			$message = "Success";
		}
		$send = [
			'success' => $success,
			'message' => $message,
			'data'    => $data,
			'paging'  => [
				'before' => $currentFeedId,
				'after'  => $afterFeedId
			]
		];
		return Response::json($send);
	}

	/**
	 * Get Feed
	 * @param  Feed $feed [description]
	 * @param  Integer $idMember [Is id of member (not member_id of member)]
	 * @return array Feed
	 */
	public function getListFeed($feeds, $idMember){
		$data = array();
		foreach($feeds as $item){

			$arr_image = $item->image()->get();
			$images = array();
			if(count($arr_image)==0){
				$images = null;
			}else{
				foreach($arr_image as $img){
					$images[] = [
						'imageId'           => $img->id,
						'urlImage'          => URLWEB.$img->url_image,
						'type'              => $img->type,
						'linkFace'          => $img->link_face,
						'urlImageThumbnail' => URLWEB.$img->url_image_thumbnail
					];
				}
			}
			

			$vde = $item->video()->first();
			$video = array();
			if(isset($vde->id)){
				$video = array();
				$video['videoId']  = $vde->id;
				$video['urlVideo'] = URLWEB.$vde->url_video;
				$video['urlVideoThumbnail'] = URLWEB.$vde->url_image_thumbnail;
				$video['type']     = $vde->type;
			}else{
				$video = null;
			}

			
			
			$mem = $item->member()->first();
			$member= array();
			$member['memberId']   =$mem->id;
			$member['username']   =$mem->username;
			// $member['rank']       =$mem->rank;
			// $member['like']       =$mem->like;
			$member['avatarUrl']  =$mem->avatar_url;
			// $member['totalImage'] =$mem->total_image ;


			$isLike = MemberLikeFeed::where('member_id', $memberId)->where('feed_id',$item->id)->first();
			if(isset($isLike->id)){
				$liked = $isLike->is_like;
			}else{
				$liked = 0;
			}

			$mdata = array();
			$mdata['feedId']  = $item->id;
			$mdata['title']   = $item->title;
			$mdata['time']    = $item->time;
			$mdata['isLike']  = $liked;
			$mdata['like']    = $item->like;
			$mdata['comment'] = $item->comment;
			// $mdata['share']   = $item->share;
			$mdata['school']  = $item->school;
			$mdata['images']  = $images;
			$mdata['video']  = $video;
			$mdata['member']  = $member;

			$data[] = $mdata;
		}
		return $data;
	}

	/**
	 * Get List Top Feed
	 * 
	 * @return Response
	 */
	public function getTopFeed(Request $request){
		//Get distance with now and Monday of this week
		// $distance= floor((strtotime ("now")- strtotime("last Monday"))/86400);
		
		// Get date of Monday of last week
		// $da = date('Y-m-d', strtotime("- ".($distance+7)." day"));
		// echo $da;die;
		 
		// Get number week of year
		$week = date('W', strtotime(date('Y-m-d')));
		
		// Get data from client
		$memberId = $request->input('memberId');
		$listIdUsed = $request->input('listIdUsed');
		$limit      = $request->input('limit');
		$type       = (int)$request->input('type');

		//If member not login
		// if($memberId==''){
		// 	$idMember = '';		
		// }else{
		// 	$member = Member::where('id',$memberId)->first();
		// 	if(isset($member->id)){
		// 		$idMember = $member->id;
		// 	}else{
		// 		$idMember = '';
		// 	}
		// }
		
		//Add ids to array
		$arr_idUsed = explode(',', $listIdUsed);

		// Get list feed order by vote and not used
		// If 0: last week
		// If 1: last month
		// Else all
		switch($type){
			case 0:
				$feeds = Feed::whereNotIn('id', $arr_idUsed)->where(DB::raw('YEAR(time)'),'=',date('Y'))->where(DB::raw('WEEKOFYEAR(time)'),'=',$week-1)->orderBy('vote','DESC')->orderBy('id','DESC')->take($limit)->get(); 
				break;
			case 1:
				$feeds = Feed::whereNotIn('id', $arr_idUsed)->where(DB::raw('YEAR(time)'),'=',date('Y'))->where(DB::raw('MONTH(time)'),'=',date('m')-1)->orderBy('vote','DESC')->orderBy('id','DESC')->take($limit)->get();
				break;
			default:
				$feeds = Feed::whereNotIn('id', $arr_idUsed)->orderBy('vote','DESC')->orderBy('id','DESC')->take($limit)->get();
		}
		if(count($feeds)==0){
			$success = 0;
			$data = [];
			$afterListIdUsed=0;
		} else{
			$afterListIdUsed = $listIdUsed;
			foreach($feeds as $item){
				$afterListIdUsed.=', '.$item->id;
			}
			$data = $this->getListFeed($feeds,$memberId);

			$success = 1;
		}
		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Feed':'Success',
			'data'    => $data,
			'paging'  => [
				'before' => $listIdUsed,
				'after'  => $afterListIdUsed
			]
		];
		return Response::json($send);
	}

	/**
	 * Update when like or unlike 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function postLike(Request $request){
		// Get data form client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$type = $request->input('type');

		// Member not login
		if($memberId==''){
			return Response::json([
			'success' => 0,
			'message' =>'Not login'
			]);
		}

		//Update like of feed and like of member
		$feed = Feed::find($feedId);
		if(isset($feed->id)){
			$member = $feed->member()->first();
			if($type==1){
				$feed->like++;
				$member->like++;
			}
			else{
				$feed->like--;
				$member->like--;
			}
			$feed->save();
			$member->save();
		}

		//Get id of member has member_id = memberId
		//If this is first time member like feed then create 1 record on table
		//MemberLikeFeed else update is_like for record
		// $memberLike = Member::where('id',$memberId)->first();
		$isLike = MemberLikeFeed::where('member_id',$memberId)->where('feed_id', $feedId)->first();
		
		if(isset($isLike->id)){
			$isLike->is_like=$type;
			$isLike->save();
		}else{
			$isLike=new MemberLikeFeed;
			
			$isLike->member_id = $memberId;
			$isLike->feed_id = $feedId;
			$isLike->is_like = $type;
			$isLike->save();
		}
		// $success = $this->postUpdate($request, 'like');
		return Response::json([
			'success' => 1,
			'message' => 'Success'
			]);
	}

	/**
	 * Test Post Feed
	 * @return [type] [description]
	 */
	// public function testPostFeed(){
	// 	return view('testPostFeed');
	// }
	// 
	public function getFeed(Request $request){

		$feedId = $request->input('feedId');
		$memberId = $request->input('memberId');
		// $idMember = Member::where('member_id',$memberId)->first();
		$feed = Feed::where('id',$feedId)->get();
		if(isset($feed->first()->id)){
			$data = $this->getListFeed($feed, $memberId);
			return Response::json([
				'success'=>1,
				'message'=>'Success',
				'data'=>$data
				]);
		}else{
			return Response::json([
				'success'=>0,
				'message'=>'Không Tìm Thấy Feed',
				'data'=>null
				]);
		}
	}

}
