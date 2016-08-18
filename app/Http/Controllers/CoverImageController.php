<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\ImageCover;
use Response;

class CoverImageController extends Controller {

	/**
	 * Get List Cover Image
	 * @return Response
	 */
	public function getCoverImage(Request $request){
		// Get data from client
		$listIdUsed = $request->input('listIdUsed');
		$limit      = $request->input('limit');

		//Add ids to array
		$arr_idUsed = explode(',', $listIdUsed);

		// print_r($arr_idUsed);die;
		// Get list image cover order by number cover and not used
		$imagecover = ImageCover::whereNotIn('id', $arr_idUsed)->orderBy('number_cover','DESC')->take($limit)->get();
		if(count($imagecover)==0){
			$success = 0;
			$data = [];
			$afterListIdUsed=0;
		} else{
			$data = array();
			$afterListIdUsed = $listIdUsed;
			foreach($imagecover as $item){
				$image = [
					'imageId'=> $item->id,
					'title' => $item->title,
					'urlImage' => $item->url_image,
					'urlImageThumbnail'=>$item->url_image_thumbnail,
					'numberCover' => $item->number_cover
				];
				$afterListIdUsed.=','.$item->id;
				$data[] = $image;
			}
			$success = 1;
		}

		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Image':'Success',
			'data'    => $data,
			'paging'  => [
				'beforeListIdUsed' => $listIdUsed,
				'afterListIdUsed'  => $afterListIdUsed
			]
		];
		return Response::json($send);
	}

}
