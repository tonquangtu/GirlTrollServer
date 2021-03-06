<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;
use Response;
use App\Event;
use App\UserEvent;
use App\ImageEvent;
use App\Feed;

class EventController extends Controller {
	/**
	 * [getListEventActive description]
	 * @return [type] [description]
	 */
	public function getListEvent(){
		
		$listEvent = Event::orderBy('time_end', 'DESC')->get();
		if (count($listEvent) == 0) {
			$success = 0;
			$data = [];
		} else {
			$success = 1;
			$data = array();
			foreach ($listEvent as $event) {
				$imageevents = ImageEvent::where('event_id','=',$event->id)->get();
				$data[] = [
					'eventId' => $event->id,
					'title'=>$event->title,
					'urlImageEvent'=>URLWEB.$event->url_image_event,
					'timeEvent' => [
						'timeStart' => $event->time_start,
						'timeEnd' => $event->time_end
					],
					'shortContent' => $event->short_content,
					'content' => $event->content,
					// 'images' => $img,
					'type' => $event->type,
					'policy' => $event->policy,
					'active' => $event->active,
					'numberImage'=>count($imageevents)
				];
			}
		}
		$send = [
			'success' => $success,
			'message' => ($success == 0) ? 'Không tìm thấy sự kiện' : 'List of events',
			'data' => $data
		];
		return Response::json($send);
	}

	/**
	 * List event active
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$id = (int)$request->input('eventId');

		if($id==0){
			$listEvent = Event::where('active', '=', 1)->where(DB::raw('time_end'),'>',date('Y-m-d H:i:s'))->orderBy('time_end', 'DESC')->get();
			if (count($listEvent) == 0) {
				$success = 0;
				$data = [];
			} else {
				$success = 1;
				$data = array();
				foreach ($listEvent as $event) {
					$data[] = [
						'eventId' => $event->id,
						'title' => $event->title,
						'urlImageEvent' => URLWEB.$event->url_image_event,
						'timeEvent' => [
							'timeStart' => $event->time_start,
							'timeEnd' => $event->time_end
						]
					];
				}
			}
			$send = [
				'success' => $success,
				'message' => ($success == 0) ? 'Không tìm thấy sự kiện' : 'List of events',
				'data' => $data
			];
			return Response::json($send);
		}else{
			

			$event = Event::find($id);
			$img = array();
			if (!isset($event->id)) {
				$success = 0;
				$message = "Không tìm thấy sự kiện";
				$event = null;
			} else {

				$imageevents = ImageEvent::where('event_id','=',$event->id)->get();
				if(count($imageevents)==0){
					$success = 0;
					$message = "Không có ảnh";
					$event = null;
				}else{
					
					$userEvent = UserEvent::where('event_id', '=', $id) ->where('member_id', '=', $request->memberId)->first();
					if (count($userEvent) == 1) {
						$success = 0;
						$message = "Đã tham gia sự kiện";
					}else{
						$success = 1;
						$message = "Success";
					}
					foreach($imageevents as $item){
						$image = $item->image()->first();
						$img[] = [
							'feedId' => $image->feed_id,
							'imageId' => $image->id,
							'urlImage' => URLWEB.$image->url_image,
							'type' => $image->type,
							'linkFace' => $image->link_face,
							'urlImageThumbnail' => URLWEB.$image->url_image_thumbnail
						];
					}
					$event = [
						'title'=>$event->title,
						'urlImageEvent'=>URLWEB.$event->url_image_event,
						'timeEvent' => [
							'timeStart' => $event->time_start,
							'timeEnd' => $event->time_end
						],
						'shortContent' => $event->short_content,
						'content' => $event->content,
						'images' => $img,
						'type' => $event->type,
						'policy' => $event->policy,
						'active' => $event->active
					];
				}
			}
			$send = [
				'success' => $success,
				'message' => $message,
				'data' => $event
			];
			return Response::json($send);
		}
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$event = Event::find($id);
		$img = array();
		if (!isset($event->id)) {
			return Response::json([
					'success'=>0,
					'message'=>'Không tìm thấy sự kiện',
					'data'=>null
				]);
		} else {
			
			$imageevents = ImageEvent::where('event_id','=',$event->id)->get();
			if(count($imageevents)==0){
				$success = 0;
				$img = null;
			}
			foreach($imageevents as $item){
				$image = $item->image()->first();
				$img[] = [
					'feedId' => $image->feed_id,
					'imageId' => $image->id,
					'urlImage' => URLWEB.$image->url_image,
					'type' => $image->type,
					'linkFace' => $image->link_face,
					'urlImageThumbnail' => URLWEB.$image->url_image_thumbnail
				];
			}

			$send = [
				'success' => 1,
				'message' => 'Chi tiết thông tin sự kiện',
				'data' => [
					'eventId'=>$event->id,
					'title'=>$event->title,
					'urlImageEvent'=>URLWEB.$event->url_image_event,
					'timeEvent' => [
						'timeStart' => $event->time_start,
						'timeEnd' => $event->time_end
					],
					'shortContent' => $event->short_content,
					'content' => $event->content,
					'images' => $img,
					'type' => $event->type,
					'policy' => $event->policy,
					'active' => $event->active
				]
			];
			return Response::json($send);
		}
		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$event = new Event;
		$event->time_start = date_format(date_create($request->input('timeStart')),'Y-m-d');
		$event->time_end = date_format(date_create($request->input('timeEnd')),'Y-m-d');
		if(strtotime($event->time_end)-strtotime($event->time_start)<=0){
			$success = 0;
			$message = 'Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc';
		}else{
			$event->title = $request->input('title');
			$event->short_content = $request->input('shortContent');
			$event->content = $request->input('content');
			$event->type = $request->input('type');
			$event->url_image_event = 'public/event/event'.$request->input('type').'.jpg';
			$event->policy = $request->input('policy');
			$event->active = $request->input('active');
			$event->save();
			$success = 1;
			$message = 'Thêm sự kiện thành công';
		}
		$send = [
			'success' => $success,
			'message' => $message,
		];
		return Response::json($send);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$event = Event::find($id);
		$event->time_start = date_format(date_create($request->input('timeStart')),'Y-m-d');
		$event->time_end = date_format(date_create($request->input('timeEnd')),'Y-m-d');
		if(strtotime($event->time_end)-strtotime($event->time_start)<=0){
			$success = 0;
			$message = 'Time Start smaller than time end';
		}else{
			$event->title = $request->input('title');
			$event->short_content = $request->input('shortContent');
			$event->content = $request->input('content');
			$event->type = $request->input('type');
			$event->url_image_event = 'public/event/event'.$request->input('type').'.jpg';
			$event->policy = $request->input('policy');
			$event->active = $request->input('active');
			$event->save();
			$success = 1;
			$message = 'Update Success';
		}
		$send = [
			'success' => $success,
			'message' => $message,
		];
		return Response::json($send);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$event = Event::find($id);
		if(isset($event->id)){
			$listImage = ImageEvent::where('event_id',$id)->get();
			foreach($listImage as $item){
				$item->delete();
			}

			$listMemberJoinEvent = UserEvent::where('event_id',$id)->get();
			foreach($listMemberJoinEvent as $item){
				$item->delete();
			}

			$event->delete();
			return Response::json([
			'success' => 1,
			'message' => 'Xóa sự kiện thành công',
		]);
		}
		return Response::json([
			'success' => 0,
			'message' => 'Không tìm thấy sự kiện',
		]);
	}

	/**
	 * Post if member complete event
	 * @return Response
	 */
	public function postEventComplete(Request $request){
		$userEvent = UserEvent::where('event_id', '=', $request->eventId)
			->where('member_id', '=', $request->memberId)->first();
		if (count($userEvent) == 1) {
			$success = 0;
		} else {
			$success = 1;
			$userEvent = new UserEvent();
			$userEvent->event_id = $request->eventId;
			$userEvent->member_id = $request->memberId;
			$userEvent->image_id = $request->imageId;
			$userEvent->save();
		}
		$send = [
			'success' => $success,
			'message' => ($success == 0) ? 'Bạn đã tham gia sự kiện này' : 'Success'
		];
		return Response::json($send);
	}

	public function getAddImageEvent($id){
		$event = Event::find($id);
		if(!isset($event->id)){
			return Response::json([
				'success'=>0,
				'message'=>'Not found event'
			]);
		}
		$imageevents = ImageEvent::where('event_id','=',$id)->get();
		$images = array();
		if(count($imageevents)!=0){
			foreach($imageevents as $item){
				$img = $item->image()->first();
				$images[] = [
					'imageId'           => $img->id,
					'urlImage'          => URLWEB.$img->url_image,
					'linkFace'          => $img->link_face,
					'urlImageThumbnail' => URLWEB.$img->url_image_thumbnail,
					'checked'			=> 1
				];
			}
		}
		// Get number week of year
		$week = date('W', strtotime(date('Y-m-d')));
		
		// Get 30 feed of top week
		$limit      = 30;

		// Get list feed order by vote
		// $feeds = Feed::where('checked','=',1)->where(DB::raw('YEAR(time)'),'=',date('Y'))->where(DB::raw('WEEKOFYEAR(time)'),'=',$week-1)->orderBy('vote','DESC')->take($limit)->get(); 
		$feeds = Feed::where('checked','=',1)->where(DB::raw('YEAR(time)'),'=',date('Y'))->orderBy('vote','DESC')->take($limit)->get(); 


		if(count($feeds)!=0){

			//Get all of image of 30 feed of top week
			foreach($feeds as $item){
				$arr_image = $item->image()->get();
				foreach($arr_image as $img){
					$images[] = [
						'imageId'           => $img->id,
						'urlImage'          => URLWEB.$img->url_image,
						'linkFace'          => $img->link_face,
						'urlImageThumbnail' => URLWEB.$img->url_image_thumbnail,
						'checked'			=> 0
					];
				}
			}
		}
		if(count($images)==0){
			// return redirect()->route('getListEvent')->with(['flash_level'=>'danger','flash_message'=>'Not found Image to Add']);
			return Response::json([
				'success'=>0,
				'message'=>'Chưa có ảnh nào để thêm'
			]);
		}
		return Response::json([
			'success'=>1,
			'message'=>'Success',
			'data'=>[
				'images'=>$images,
				'id' => $id,
				'numberAdded'=>count($imageevents)
			]
		]);	
	}

	public function postAddImageEvent(Request $request, $id){
		$images = $request->input('images');
		if(count($images)==0){
			return Response::json([
				'success'=>0,
				'message'=>'Thêm ảnh không thành công'
			]);
		}
		foreach($images as $image){
			$imageevent = ImageEvent::where('image_id','=',$image)->where('event_id','=',$id)->first();
			if(count($imageevent)>0){
				$imageevent->delete();
			}
			$imageevent = new ImageEvent;
			$imageevent->image_id = $image;
			$imageevent->event_id = $id;
			$imageevent->save();
		}
		return Response::json([
			'success'=> 1,
			'message'=> 'Thêm ảnh cho sự kiện thành công'
		]);
	}
}
