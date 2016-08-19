<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;
use Response;
use App\Event;
use App\UserEvent;

class EventController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$listEvent = Event::all();
//		$listEvent = DB::table('event')->get();
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
		$event = Event::where('id', '=', $id)->first();
		if (!isset($event->id)) {
			$success = 0;
			$img = [];
		} else {
			$success = 1;
			$images = DB::table('event')
				->join('user_event', 'event.id', '=', 'user_event.event_id')
				->join('image', 'image.id', '=', 'user_event.image_id')
				->select('image.*')
				->get();
			foreach ($images as $image) {
				$img[] = [
					'feedId' => $image->feed_id,
					'imageId' => $image->id,
					'urlImage' => $image->url_image,
					'type' => $image->type,
					'linkFace' => $image->link_face,
					'urlImageThumbnail' => $image->url_image_thumbnail
				];
			}
		}
		$send = [
			'success' => $success,
			'message' => ($success == 0) ? 'Not found event' : 'Detail information of event',
			'event' => [
				'sortContent' => $event->sort_content,
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
}
