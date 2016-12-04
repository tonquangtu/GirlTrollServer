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
use App\ImageCover, App\HotFeed, App\Comment;
use Thumbnail, File;

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
		$feed->checked = 0;
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

			//If post coverimage
			if($typeImage==1){
				$idcoverimage = $request->input('coverImageId');
				$imagecover = ImageCover::find($idcoverimage);
				if(isset($imagecover->id)){
					$imagecover->number_cover++;
					$imagecover->save();
				}
			}

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
				//Check link youtube
				$youtube = $request->input('youtube');
				if(strpos($youtube,'youtube.com/watch?v=')>0){
		        	$youtube_id = str_replace('https://www.youtube.com/watch?v=','',$youtube);
		        	$thumbnailVideo = 'http://img.youtube.com/vi/'.$youtube_id.'/0.jpg';
		        	$vde = new Video;
					$vde->url_video = $youtube;
					$vde->url_image_thumbnail = $thumbnailVideo;
					$vde->type = $typeVideo;
					$vde->feed_id = $feed->id;
					$vde->save();

					$success = 1;
					$message = "Success";
		        } else{
		        	$feed->delete();
		        	return Response::json([
						'success'=>0,
						'message'=>"Không phải link youtube"
					]);
		        }
				// $thumbnailname = $request->input('thumbnailVideo');
				
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
	 * Destroy feed
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function destroy($id){
		$feed = Feed::find($id);
		if(isset($feed->id)){
			$images = $feed->image()->get();
			$video = $feed->video()->first();
			$member = $feed->member()->first();
			$memberlikefeed= $feed->memberlikefeed()->get();
			$comment = $feed->comment()->get();
			$hotfeed = $feed->hotfeed()->get();

			foreach($images as $item){
				File::delete(base_path().'/'.$item->url_image);
				File::delete(base_path().'/'.$item->url_image_thumbnail);
				$imageevent = $item->imageevent()->get();
				foreach($imageevent as $i){
					$i->delete();
				}

				$userevent=$item->userevent()->get();
				foreach($userevent as $j){
					$j->delete();
				}
				$item->delete();
			}
			if(isset($video->id)){
				File::delete(base_path().'/'.$video->url_video);
				File::delete(base_path().'/'.$video->url_image_thumbnail);
				$video->delete();
			}

			$member->like-=$feed->like;
			$member->total_image-=count($images);
			$member->save();

			foreach($memberlikefeed as $item){
				$item->delete();
			}
			foreach($comment as $item){
				$item->delete();
			}
			foreach($hotfeed as $item){
				$item->delete();
			}
			$feed->delete();
			return Response::json(['success'=>1,'message'=>'Success']);

		}else{
			return Response::json(['success'=>0, 'message'=>'Không tồn tại ']);
		}
	}


	public function deleteHotFeed($id){
		$feedhot = HotFeed::where('feed_id',$id)->get();
		foreach($feedhot as $item){
			$item->delete();
		}
		return Response::json([
			'success'=>1,
			'message'=>'Success'
			]);
	}
	/**
	 * Update checked for feed
	 */
	public function update(Request $request, $id){
		$feed = Feed::find($id);
		if(isset($feed->id)){
			$checked = $request->input('checked');
			$feed->checked=$checked;
			$feed->save();
			return Response::json([
					'success'=>1,
					'message'=>'Success'
				]);
		}else{
			return Response::json([
					'success'=>0,
					'message'=>'Không tìm thấy bài đăng'
				]);
		}
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
		$img->resize($width, null,function($con){$con->aspectRatio();})->save($pathThumb.'/'.$imagename);

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

		//Error if end of feed
		$firstfeed = Feed::where('checked','>',0)->orderBy('id','ASC')->first();
		if(!isset($firstfeed->id)||$currentFeedId==$firstfeed->id||$currentFeedId==0){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentFeedId == -1 => the first load new feed
			if($currentFeedId == -1){
				$current = Feed::where('checked','>',0)->orderBy('id','ASC')->get()->last()->id+1;
			} else{
				$current = $currentFeedId;
			}

			$feeds = Feed::where('checked','>',0)->where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
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

	/**
	 * Get list feed for admin
	 * @return [type] [description]
	 */
	public function getListFeedAdmin(Request $request){
		// Get data from client
		$memberId = '';
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');

		//Error if end of feed
		$firstfeed = Feed::orderBy('id','ASC')->first();
		if(!isset($firstfeed->id)||$currentFeedId==$firstfeed->id||$currentFeedId==0){
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

	/**
	 * Get list hot feed by admin
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getListHotFeedAdmin(Request $request){
		// Get data from client
		$memberId = '';
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');

		//Error if end of feed
		//
		$firstfeed = HotFeed::orderBy('id','ASC')->first();
		if(!isset($firstfeed->id)||$currentFeedId==$firstfeed->id||$currentFeedId==0){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentFeedId == -1 => the first load new feed
			if($currentFeedId == -1){
				$current = HotFeed::orderBy('id','ASC')->get()->last()->id+1;
			} else{
				$current = $currentFeedId;
			}
			$hotfeeds = HotFeed::where('id','<',$current)->orderBy('id','DESC')->take($limit)->get();
			
			//Set default
			$images=null;
			$video = null;
			
			foreach($hotfeeds as $i){
				$item=$i->feed()->first();
				if($i->type==0){
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
				}else{
					$vde = $item->video()->first();
					$video = array();

					if(isset($vde->id)){

						//If video from youtube urlthumbnail is urlthumbnail of youtube video
						if($vde->type==1){
							$urlVideoThumbnail1 = $vde->url_image_thumbnail;
							$urlVideo1=$vde->url_video;
						}else{
							$urlVideoThumbnail1 = URLWEB.$vde->url_image_thumbnail;
							$urlVideo1 = URLWEB.$vde->url_video;
						}
						$video = array();
						$video['videoId']  = $vde->id;
						$video['urlVideo'] = $urlVideo1;
						$video['urlVideoThumbnail'] = $urlVideoThumbnail1;
						$video['type']     = $vde->type;
					}else{
						$video = null;
					}
				}

				$mem = $item->member()->first();
				if(isset($mem->id)){
					$member= array();
					$member['memberId']   =$mem->id;
					$member['username']   =$mem->username;
					// $member['rank']       =$mem->rank;
					// $member['like']       =$mem->like;
					$member['avatarUrl']  =$mem->facebook_id==''?URLWEB.$mem->avatar_url:$mem->avatar_url;
					// $member['totalImage'] =$mem->total_image ;
				}else{
					$member = null;
				}


				$isLike = MemberLikeFeed::where('member_id', $memberId)->where('feed_id',$item->id)->first();
				if(isset($isLike->id)){
					$liked = $isLike->is_like;
				}else{
					$liked = 0;
				}

				$isComment = Comment::where('member_id',$memberId)->where('feed_id',$item->id)->first();
				if(isset($isComment->id)){
					$commented = 1;
				}else{
					$commented = 0;
				}

				$mdata = array();
				$mdata['feedId']  = $item->id;
				$mdata['title']   = $item->title;
				$mdata['time']    = $item->time;
				$mdata['isLike']  = $liked;
				$mdata['isComment']  = $commented;
				$mdata['like']    = $item->like;
				$mdata['comment'] = $item->comment;
				// $mdata['share']   = $item->share;
				$mdata['school']  = $item->school;
				$mdata['images']  = $images;
				$mdata['video']  = $video;
				$mdata['member']  = $member;

				$data[] = $mdata;
			}
			
			$success = 1;
			$afterFeedId = (int)$hotfeeds->last()->id;
		}
		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Hot Feed':'Success',
			'data'    => $data,
			'paging'  => [
				'before' => $currentFeedId,
				'after'  => $afterFeedId
			]
		];
		return Response::json($send);
	}
	/**
	 * Get feed refresh
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getFeedRefresh(Request $request){
		// Get data from client
		$memberId = $request->input('memberId');
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');

		if($currentFeedId == -1){
			// $feeds = Feed::where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$feeds = Feed::where('checked','>',0)->orderBy('id','DESC')->take($limit)->get();
			
			$data = $this->getListFeed($feeds, $memberId);

			$success = 1;
			$afterFeedId = (int)$feeds->first()->id;
			$message = "Success";
		}else if($currentFeedId==Feed::where('checked','>',0)->orderBy('id','ASC')->get()->last()->id){
			$data = null;
			$success = 1;
			$afterFeedId = $currentFeedId;
			$message = "Feed Mới Nhất";
		} else {
			$feeds = Feed::where('checked','>',0)->where('id','>',$currentFeedId)->orderBy('id','ASC')->take($limit)->get();
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
	public function getListFeed($feeds, $memberId){
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

				//If video from youtube urlthumbnail is urlthumbnail of youtube video
				if($vde->type==1){
					$urlVideoThumbnail1 = $vde->url_image_thumbnail;
					$urlVideo1=$vde->url_video;
				}else{
					$urlVideoThumbnail1 = URLWEB.$vde->url_image_thumbnail;
					$urlVideo1 = URLWEB.$vde->url_video;
				}
				$video = array();
				$video['videoId']  = $vde->id;
				$video['urlVideo'] = $urlVideo1;
				$video['urlVideoThumbnail'] = $urlVideoThumbnail1;
				$video['type']     = $vde->type;
			}else{
				$video = null;
			}

			
			
			$mem = $item->member()->first();
			if(isset($mem->id)){
				$member= array();
				$member['memberId']   =$mem->id;
				$member['username']   =$mem->username;
				// $member['rank']       =$mem->rank;
				// $member['like']       =$mem->like;
				$member['avatarUrl']  =$mem->facebook_id==''?URLWEB.$mem->avatar_url:$mem->avatar_url;
				// $member['totalImage'] =$mem->total_image ;
			}else{
				$member = null;
			}


			$isLike = MemberLikeFeed::where('member_id', $memberId)->where('feed_id',$item->id)->first();
			if(isset($isLike->id)){
				$liked = $isLike->is_like;
			}else{
				$liked = 0;
			}

			$isComment = Comment::where('member_id',$memberId)->where('feed_id',$item->id)->first();
			if(isset($isComment->id)){
				$commented = 1;
			}else{
				$commented = 0;
			}

			$mdata = array();
			$mdata['feedId']  = $item->id;
			$mdata['title']   = $item->title;
			$mdata['time']    = $item->time;
			$mdata['isLike']  = $liked;
			$mdata['isComment'] = $commented;
			$mdata['like']    = $item->like;
			$mdata['comment'] = $item->comment;
			// $mdata['share']   = $item->share;
			$mdata['school']  = $item->school;
			$mdata['images']  = $images;
			$mdata['video']  = $video;
			$mdata['member']  = $member;
			$mdata['checked'] = $item->checked;
			$data[] = $mdata;
		}
		return $data;
	}

	/**
	 * Get hot feed image, hot feed video
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getHotFeed(Request $request){
		// Get data from client
		$memberId = $request->input('memberId');
		$type = $request->input('type');
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');

		//Error if end of feed
		//
		$firstfeed = HotFeed::where('type',$type)->orderBy('id','ASC')->first();
		if(!isset($firstfeed->id)||$currentFeedId==$firstfeed->id||$currentFeedId==0){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentFeedId == -1 => the first load new feed
			if($currentFeedId == -1){
				$current = HotFeed::where('type',$type)->orderBy('id','ASC')->get()->last()->id+1;
			} else{
				$current = $currentFeedId;
			}
			$hotfeeds = HotFeed::where('type',$type)->where('id','<',$current)->orderBy('id','DESC')->take($limit)->get();
			
			//Set default
			$images=null;
			$video = null;
			
			foreach($hotfeeds as $i){
				$item=$i->feed()->first();
				if($type==0){
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
				}else{
					$vde = $item->video()->first();
					$video = array();

					if(isset($vde->id)){

						//If video from youtube urlthumbnail is urlthumbnail of youtube video
						if($vde->type==1){
							$urlVideoThumbnail1 = $vde->url_image_thumbnail;
							$urlVideo1=$vde->url_video;
						}else{
							$urlVideoThumbnail1 = URLWEB.$vde->url_image_thumbnail;
							$urlVideo1 = URLWEB.$vde->url_video;
						}
						$video = array();
						$video['videoId']  = $vde->id;
						$video['urlVideo'] = $urlVideo1;
						$video['urlVideoThumbnail'] = $urlVideoThumbnail1;
						$video['type']     = $vde->type;
					}else{
						$video = null;
					}
				}

				$mem = $item->member()->first();
				if(isset($mem->id)){
					$member= array();
					$member['memberId']   =$mem->id;
					$member['username']   =$mem->username;
					// $member['rank']       =$mem->rank;
					// $member['like']       =$mem->like;
					$member['avatarUrl']  =$mem->facebook_id==''?URLWEB.$mem->avatar_url:$mem->avatar_url;
					// $member['totalImage'] =$mem->total_image ;
				}else{
					$member = null;
				}


				$isLike = MemberLikeFeed::where('member_id', $memberId)->where('feed_id',$item->id)->first();
				if(isset($isLike->id)){
					$liked = $isLike->is_like;
				}else{
					$liked = 0;
				}

				$isComment = Comment::where('member_id',$memberId)->where('feed_id',$item->id)->first();
				if(isset($isComment->id)){
					$commented = 1;
				}else{
					$commented = 0;
				}

				$mdata = array();
				$mdata['feedId']  = $item->id;
				$mdata['title']   = $item->title;
				$mdata['time']    = $item->time;
				$mdata['isLike']  = $liked;
				$mdata['isComment']  = $commented;
				$mdata['like']    = $item->like;
				$mdata['comment'] = $item->comment;
				// $mdata['share']   = $item->share;
				$mdata['school']  = $item->school;
				$mdata['images']  = $images;
				$mdata['video']  = $video;
				$mdata['member']  = $member;

				$data[] = $mdata;
			}
			
			$success = 1;
			$afterFeedId = (int)$hotfeeds->last()->id;
		}
		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Hot Feed':'Success',
			'data'    => $data,
			'paging'  => [
				'before' => $currentFeedId,
				'after'  => $afterFeedId
			]
		];
		return Response::json($send);
	}

	/**
	 * Get List Top Feed
	 * 
	 * @return Response
	 */
	public function getTopFeed(Request $request){
		// Get number week of year
		$week = date('W', strtotime(date('Y-m-d')));
		
		// Get data from client
		$memberId = $request->input('memberId');
		$listIdUsed = $request->input('listIdUsed');
		$limit      = $request->input('limit');
		$type       = (int)$request->input('type');

		//Add ids to array
		$arr_idUsed = explode(',', $listIdUsed);

		// Get list feed order by vote and not used
		// If 0: last week
		// If 1: last month
		// Else all
		switch($type){
			case 0:
				$feeds = Feed::where('checked','>',0)->whereNotIn('id', $arr_idUsed)->where(DB::raw('YEAR(time)'),'=',date('Y'))->where(DB::raw('WEEKOFYEAR(time)'),'=',$week-1)->orderBy('vote','DESC')->orderBy('id','DESC')->take($limit)->get(); 
				break;
			case 1:
				$feeds = Feed::where('checked','>',0)->whereNotIn('id', $arr_idUsed)->where(DB::raw('YEAR(time)'),'=',date('Y'))->where(DB::raw('MONTH(time)'),'=',date('m')-1)->orderBy('vote','DESC')->orderBy('id','DESC')->take($limit)->get();
				break;
			default:
				$feeds = Feed::where('checked','>',0)->whereNotIn('id', $arr_idUsed)->orderBy('vote','DESC')->orderBy('id','DESC')->take($limit)->get();
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
			$feed->vote = $feed->like*0.5+$feed->comment*0.5;
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
	 * Get feed by feedId
	 * @return [type] [description]
	 */
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

	/**
	 * Get all feed of member
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getFeedOfMember(Request $request){

		$memberId = $request->input('memberId');
		// $idMember = Member::where('member_id',$memberId)->first();
		$feed = Feed::where('checked','>',0)->where('member_id',$memberId)->get();

		if(count($feed)>0){
			$data = $this->getListFeed($feed, $memberId);
			return Response::json([
				'success'=>1,
				'message'=>'Success',
				'data'=>$data
				]);
		}else{
			return Response::json([
				'success'=>0,
				'message'=>'Bạn chưa đăng bài viết nào!',
				'data'=>null
				]);
		}
	}

	/**
	 * Get Feed Was Like (History of member)
	 * @return [type] [description]
	 */
	public function getHistory(Request $request){
		// Get data from client
		$memberId = $request->input('memberId');
		$currentFeedId = (int)$request->input('currentFeedId');
		$limit         = $request->input('limit');
		$type = $request->input('type');
		if($type=='liked'){
			$firstfeed = MemberLikeFeed::where('member_id',$memberId)->where('is_like','1')->orderBy('id','ASC')->first();
		}else if($type=='commented'){
			$firstfeed = Comment::where('member_id',$memberId)->groupBy('feed_id')->orderBy('id','ASC')->first();
		}else
			$firstfeed = Feed::where('checked','>',0)->where('member_id',$memberId)->orderBy('id','ASC')->first();
		//Error if end of feed
		// $firstfeed = MemberLikeFeed::where('member_id',$memberId)->where('is_like','1')->orderBy('id','ASC')->first();
		if(!isset($firstfeed->id)||$currentFeedId==$firstfeed->id||$currentFeedId==0){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentFeedId == -1 => the first load new feed
			if($currentFeedId == -1){
				if($type=='liked')
					$current = MemberLikeFeed::where('member_id',$memberId)->where('is_like','1')->orderBy('id','ASC')->get()->last()->id+1;
				else if($type=='commented')
					$current = Comment::where('member_id',$memberId)->groupBy('feed_id')->orderBy('id','ASC')->get()->last()->id+1;
				else
					$current = Feed::where('checked','>',0)->where('member_id',$memberId)->orderBy('id','ASC')->get()->last()->id+1;

			} else{
				$current = $currentFeedId;
			}


			if($type=='liked')
				$feedhistorys = MemberLikeFeed::where('member_id',$memberId)->where('is_like','1')->where('id','<',$current)->orderBy('id','DESC')->take($limit)->get();
			else if($type=='commented')
				$feedhistorys = Comment::where('member_id',$memberId)->where('id','<',$current)->groupBy('feed_id')->orderBy('id','DESC')->take($limit)->get();
			else
				$feedhistorys = Feed::where('checked','>',0)->where('member_id',$memberId)->where('id','<',$current)->orderBy('id','DESC')->take($limit)->get();
			
			foreach($feedhistorys as $i){
				
				if($type=='commented'||$type=='liked')
					$item = $i->feed()->first();
				else 
					$item = $i;
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

					//If video from youtube urlthumbnail is urlthumbnail of youtube video
					if($vde->type==1){
						$urlVideoThumbnail1 = $vde->url_image_thumbnail;
						$urlVideo1=$vde->url_video;
					}else{
						$urlVideoThumbnail1 = URLWEB.$vde->url_image_thumbnail;
						$urlVideo1 = URLWEB.$vde->url_video;
					}
					$video = array();
					$video['videoId']  = $vde->id;
					$video['urlVideo'] = $urlVideo1;
					$video['urlVideoThumbnail'] = $urlVideoThumbnail1;
					$video['type']     = $vde->type;
				}else{
					$video = null;
				}

				$mem = $item->member()->first();
				if(isset($mem->id)){
					$member= array();
					$member['memberId']   =$mem->id;
					$member['username']   =$mem->username;
					// $member['rank']       =$mem->rank;
					// $member['like']       =$mem->like;
					$member['avatarUrl']  =$mem->facebook_id==''?URLWEB.$mem->avatar_url:$mem->avatar_url;
					// $member['totalImage'] =$mem->total_image ;
				}else{
					$member = null;
				}

				$isLike = MemberLikeFeed::where('member_id', $memberId)->where('feed_id',$item->id)->first();
				if(isset($isLike->id)){
					$liked = $isLike->is_like;
				}else{
					$liked = 0;
				}

				$isComment = Comment::where('member_id',$memberId)->where('feed_id',$item->id)->first();
				if(isset($isComment->id)){
					$commented = 1;
				}else{
					$commented = 0;
				}

				$mdata = array();
				$mdata['feedId']  = $item->id;
				$mdata['title']   = $item->title;
				$mdata['time']    = $item->time;
				$mdata['isLike']  = $liked;
				$mdata['isComment']  = $commented;
				$mdata['like']    = $item->like;
				$mdata['comment'] = $item->comment;
				// $mdata['share']   = $item->share;
				$mdata['school']  = $item->school;
				$mdata['images']  = $images;
				$mdata['video']  = $video;
				$mdata['member']  = $member;

				$data[] = $mdata;
		
			}

			$success = 1;
			$afterFeedId = (int)$feedhistorys->last()->id;
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

	/**
	 * Add feed to hot feed
	 * @param Request $request [description]
	 * @param [type]  $id      [description]
	 */
	public function addHotFeed(Request $request, $id){
		$feed = Feed::find($id);
		if(isset($feed->id)){
			if(isset($feed->hotfeed()->first()->id)){
				return Response::json([
						'success'=>0,
						'message'=>'Bài đăng đã tồn tại trong hot'
					]);
			}else{
				$hotfeed = new HotFeed();
				$hotfeed->feed_id = $id;
				$hotfeed->type= $feed->video()->first()==null?0:1;
				$hotfeed->save();
				return Response::json([
						'success'=>1,
						'message'=>'Success'
					]);
			}
		}else{
			return Response::json([
					'success'=>0,
					'message'=>'Không tồn tại bài đăng'
				]);
		}
	}

	public function reportFeed(Request $request, $id, $type){
		$feed = Feed::find($id);
		if(isset($feed->id)){
			$feed->checked = $type;
			$feed->save();
			return Response::json([
					'success'=>1,
					'message'=>'Success'
				]);
		}else{
			return Response::json([
					'success'=>0,
					'message'=>'Không tồn tại bài đăng'
				]);
		}
	}
}
