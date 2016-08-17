<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Feed;
use Response;
use Image;
use App\Image as ImageModel;
use App\Video, App\Member;

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
		$member = Member::where('member_id','=',$memberId)->get();
		if(count($member)==0){
			return Response::json([
					'success' => 0,
					'message' => 'Not Found Member Has Member Id'
				]);
		}

		//Add Feed
		$feed = new Feed;
		$feed->title = $title;
		$feed->school = $school;
		$feed->time = date('Y-m-d H:i:s');
		$feed->like = 0;
		$feed->comment = 0;
		$feed->share = 0;
		$feed->member_id = $member->first()->id;
		$feed->save();

		//Add image or video
		if($totalFile!=0){
			$linkFace = $request->input('linkFace');
			$typeImage = $request->input('type');

			$width = 100;
			$height = 100;
			$pathImg = 'public/image';
			$pathThumb = 'public/image/thumbnail';
			$count = 0;
			//Upload feed image
			for($i=0; $i<$totalFile; $i++){
				if($request->hasFile('file_'.$i)){
					$image = $request->file('file_'.$i);
					$imagename = time().'.'.$image->getClientOriginalExtension();
					$this->resizeImagePost($image, $imagename, $width, $height, $pathImg, $pathThumb);

					$img = new ImageModel;	
					$img->url_image = $pathImg.'/'.$imagename;
					$img->type = $typeImage;
					$img->link_face = $linkFace;
					$img->feed_id = $feed->id;
					$img->url_image_thumbnail = $pathThumb.'/'.$imagename;
					$img->save();
					$count++;
				}
			}

			if($count!=$totalFile){
				$success = 0;
				$message = "Insert Image Not Enough";
			} else {
				$success = 1;
				$message = "Success";
			}

		} else{

			//Upload feed video
			$typeVideo = $request->input('type');

			if($request->hasFile('file')){
				$video = $request->file('file');
				$videoname = time().'.'.$image->getClientOriginalExtension();
				$pathVideo = 'public/video';
				$video->move($pathVideo, $videoname);

				//['id','url_video','type','feed_id']
				$vde = new Video;
				$vde->url_video = $pathVideo.'/'.$videoname;
				$vde->type = $typeVideo;
				$vde->feed_id = $feed->id;
				$vde->save();

				$success = 1;
				$message = "Success";
			} else{
				$success = 0;
				$message = "Video Not Found";
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
		
		//Create thumbnail and save to the pathThumbnail
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
		return $this->getFeed($request, 'id');
		
	}

	/**
	 * Get List Top Feed
	 * 
	 * @return Response
	 */
	public function getTopFeed(Request $request){
		return $this->getFeed($request, 'like');
	}


	public function getFeed(Request $request, $order){
		// Get data from client
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');

		//Error if end of feed
		if($currentFeedId==1){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentFeedId == -1 => the first load new feed
			if($currentFeedId == -1){
				$current = Feed::all()->last()->id+1;
			} else{
				$current = $currentFeedId;
			}

			// $feeds = Feed::where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$feeds = Feed::where('id','<', $current)->orderBy($order,'DESC')->take($limit)->get();
			$data = array();
			foreach($feeds as $item){

				$arr_image = $item->image()->get();
				$images = array();
				foreach($arr_image as $img){
					$images[] = [
						'imageId'           => $img->id,
						'urlImage'          => $img->url_image,
						'type'              => $img->type,
						'linkFace'          => $img->link_face,
						'urlImageThumbnail' => $img->url_image_thumbnail
					];
				}

				$arr_video = Feed::find($item->id)->video()->get();
				$videos = array();
				foreach($arr_video as $vde){
					$videos[]=[
						'videoId'  => $vde->id,
						'urlVideo' => $vde->url_video,
						'type'     => $vde->type
					];
				}
				$mdata = array();
				$mdata['feedId']   = $item->id;
				$mdata['memberId'] = $item->member()->first()->member_id;
				$mdata['title']    = $item->title;
				$mdata['time']     = $item->time;
				$mdata['like']     = $item->like;
				$mdata['comment']  = $item->comment;
				$mdata['share']    = $item->share;
				$mdata['school']   = $item->school;
				$mdata['images']   = $images;
				$mdata['videos']   = $videos;

				$data[] = $mdata;
			}

			$success = 1;
			$afterFeedId = (int)$feeds->last()->id;
		}
		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Feed':'Success',
			'data'    => $data,
			'paging'  => [
				'beforeFeedId' => $currentFeedId,
				'afterFeedId'  => $afterFeedId
			]
		];
		return Response::json($send);
	}

	public function testPostFeed(){
		return view('testPostFeed');
	}

}
