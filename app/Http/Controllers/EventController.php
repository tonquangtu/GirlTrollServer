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
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$listEvent = Event::where('active', '=', 1)->orderBy('time_end', 'DESC')->get();
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
					'timeEvent' => [
						'timeStart' => $event->time_start,
						'timeEnd' => $event->time_end
					]
				];
			}
		}
		$send = [
			'success' => $success,
			'message' => ($success == 0) ? 'Not found event' : 'List of events',
			'data' => $data
		];
		return Response::json($send);
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
			$success = 0;
			$img = null;
		} else {
			
			$success = 1;
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
		}
		$send = [
			'success' => $success,
			'message' => ($success == 0) ? 'Not found event' : 'Detail information of event',
			'event' => [
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

	/**
	 * Get List Event for Web
	 * @return [type] [description]
	 */
	public function getListEvent(){
		$events = Event::paginate(10);
		$events->setPath(URLWEB.'event/list');
		return view('admin.event.list',compact('events'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.event.add');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$event = new Event;
		$event->time_start = date_format(date_create($request->input('time_start')),'Y-m-d');
		$event->time_end = date_format(date_create($request->input('time_end')),'Y-m-d');
		if(strtotime($event->time_end)-strtotime($event->time_start)<=0){
			return redirect()->route('event.create')->with(['flash_level'=>'danger','flash_message'=>'Time Start smaller than time end']);
		}
		$event->title = $request->input('title');
		$event->short_content = $request->input('short_content');
		$event->content = $request->input('content');
		$event->type = $request->input('type');
		$event->policy = $request->input('policy');
		$event->active = $request->input('active');
		$event->save();
		return redirect()->route('getListEvent')->with(['flash_level'=>'success','flash_message'=>'Add New Success']);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{	
		$event = Event::find($id);
		if(isset($event->id)){
			return view('admin.event.edit',compact('event'));
		}else{
			return redirect()->route('getListEvent')->width(['flash_level'=>'danger','flash_message'=>'Not found event']);
		}
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
		$event->time_start = date_format(date_create($request->input('time_start')),'Y-m-d');
		$event->time_end = date_format(date_create($request->input('time_end')),'Y-m-d');
		if(strtotime($event->time_end)-strtotime($event->time_start)<=0){
			return redirect()->route('event.create')->with(['flash_level'=>'danger','flash_message'=>'Time Start smaller than time end']);
		}
		$event->title = $request->input('title');
		$event->short_content = $request->input('short_content');
		$event->content = $request->input('content');
		$event->type = $request->input('type');
		$event->policy = $request->input('policy');
		$event->active = $request->input('active');
		$event->save();
		return redirect()->route('getListEvent')->with(['flash_level'=>'success','flash_message'=>'Update Success']);
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
			$event->delete();
			return redirect()->route('getListEvent')->with(['flash_level'=>'success','flash_message'=>'Delete success']);
		}
		return redirect()->route('getListEvent')->with(['flash_level'=>'danger','flash_message'=>'Not found event']);
	}

	/**
	 * Post if member complete event
	 * @return Response
	 */
	public function postEventComplete(Request $request){
		$userEvent = UserEvent::where('event_id', '=', $request->eventId)
			->where('member_id', '=', $request->memberId)->first();
		if (count($userEvent) == 0) {
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
			'message' => ($success == 0) ? 'Member and event existed in database' : 'Success'
		];
		return Response::json($send);
	}

	public function getAddImageEvent($id){
		$event = Event::find($id);
		if(!isset($event->id)){
			return redirect()->route('getListEvent')->with(['flash_level'=>'danger','flash_message'=>'Not found event']);
		}
		$imageevents = ImageEvent::where('event_id','=',$id)->get();
		$images = array();
		if(count($imageevents)!=0){
			foreach($imageevents as $item){
				$img = $item->image()->first();
				$images[] = [
					'imageId'           => $img->id,
					'urlImage'          => $img->url_image,
					'linkFace'          => $img->link_face,
					'urlImageThumbnail' => $img->url_image_thumbnail,
					'checked'			=> 1
				];
			}
		}
		// Get number week of year
		$week = date('W', strtotime(date('Y-m-d')));
		
		// Get 30 feed of top week
		$limit      = 30;

		// Get list feed order by vote
		$feeds = Feed::where(DB::raw('YEAR(time)'),'=',date('Y'))->where(DB::raw('WEEKOFYEAR(time)'),'=',$week-1)->orderBy('vote','DESC')->take($limit)->get(); 


		if(count($feeds)!=0){

			//Get all of image of 30 feed of top week
			foreach($feeds as $item){
				$arr_image = $item->image()->get();
				foreach($arr_image as $img){
					$images[] = [
						'imageId'           => $img->id,
						'urlImage'          => $img->url_image,
						'linkFace'          => $img->link_face,
						'urlImageThumbnail' => $img->url_image_thumbnail,
						'checked'			=> 0
					];
				}
			}
		}
		if(count($images)==0){
			return redirect()->route('getListEvent')->with(['flash_level'=>'danger','flash_message'=>'Not found Image to Add']);
		}
		return view('admin.event.image',compact('images','id'))->with('numberAdded',count($imageevents));
			
	}

	public function postAddImageEvent(Request $request, $id){
		$images = $request->input('images');
		if(count($images)==0){
			return redirect()->route('getListEvent')->with(['flash_level'=>'danger','flash_message'=>'Not add image']);
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
		return redirect()->route('getListEvent')->with(['flash_level'=>'success','flash_message'=>'Add Image for event success']);
	}
}
