<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\ImageCover;
use Response;
use File;
use Image;

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
					'urlImage' => URLWEB.$item->url_image,
					'urlImageThumbnail'=>URLWEB.$item->url_image_thumbnail,
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
				'before' => $listIdUsed,
				'after'  => $afterListIdUsed
			]
		];
		return Response::json($send);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$coverimage = new ImageCover;
		$coverimage->title=$request->input('title');
		if($request->hasFile('image_new')){
			$image = $request->file('image_new');
			$filename = changeTitle(time().$image->getClientOriginalName());
			$pathImg = 'public/imagecover';
			$pathThumb = 'public/imagecover/thumbnail';
			$width = 100;
			$height = 100;
			$this->resizeImageCover($image,$filename,$width,$height,$pathImg,$pathThumb);
			$coverimage->url_image = $pathImg.'/'.$filename;
			$coverimage->url_image_thumbnail = $pathThumb.'/'.$filename;
			$coverimage->save();
			return redirect()->route('getListCoverImage')->with(['flash_level'=>'success','flash_message'=>'Add new success']);
		} else{
			return redirect()->route('getAddCoverImage')->with(['flash_level'=>'danger','flash_message'=>'Not found Image']);
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
		$coverimage = ImageCover::find($id);
		if(isset($coverimage->id)){
			return Response::json([
				'success'=>1,
				'message'=>'Success',
				'data'=>$coverimage
				]);
		}
		return Response::json([
			'success'=>0,
			'message'=>'Not found cover image',
			'data'=>null
			]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$coverimage = ImageCover::find($id);
		$coverimage->title=$request->input('title');
		$coverimage->save();
		return Response::json([
				'success'=>1,
				'message'=>'Update success'
			]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$coverimage = ImageCover::find($id);
		if(isset($coverimage->id)){
			$coverimage->delete();
			if(File::exists($coverimage->url_image))
				File::delete($coverimage->url_image);
			if(File::exists($coverimage->url_image_thumbnail))
				File::delete($coverimage->url_image_thumbnail);
			return Response::json([
				'success'=>1,
				'message'=>'Delete success'
			]);
		}
		return Response::json([
				'success'=>0,
				'message'=>'Can\'t delete image'
			]);
	}

	/**
	 * Store ImageCover And Store Thumbnail
	 * @param  file   $image     
	 * @param  string $imagename 
	 * @param  int    $width     
	 * @param  int    $height    
	 * @param  string $pathImg   
	 * @param  string $pathThumb 
	 * @return null            
	 */
	public function resizeImageCover($image, $imagename, $width, $height, $pathImg, $pathThumb){

		//Move image to the pathImg
		$image->move($pathImg, $imagename);
		
		//Create thumbnail and save to the pathThumbnail
		$img = Image::make($pathImg.'/'.$imagename);
		$img->resize($width, $height)->save($pathThumb.'/'.$imagename);

		return null;
	}

}
