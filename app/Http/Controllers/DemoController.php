<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Thumbnail;

class DemoController extends Controller {

	public function demo(){
		return view('testThumbnailVideo');
	}

	public function testThumbnail(Request $request)
    {
        // get file from input data
        $file             = $request->file('file');

        // get file type
        $extension_type   = $file->getClientMimeType();

        // set storage path to store the file (actual video)
        $destination_path = 'public/demo';

        // get file extension
        $extension        = $file->getClientOriginalExtension();


        $timestamp        = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
        $file_name        = $timestamp;

        $upload_status    = $file->move($destination_path, $file_name);         

        if($upload_status)
        {
            // file type is video
            // set storage path to store the file (image generated for a given video)
            $thumbnail_path   = 'public/demo/image';

            $video_path       = $destination_path.'/'.$file_name;

            // set thumbnail image name
            $thumbnail_image  ='haha'.".".$timestamp.".jpg";

            // set the thumbnail image "palyback" video button
            $water_mark       = 'public/watermark/p.png';

            // get video length and process it
            // assign the value to time_to_image (which will get screenshot of video at that specified seconds)
            $time_to_image    = 10;


            $thumbnail_status = Thumbnail::getThumbnail($video_path,$thumbnail_path,$thumbnail_image,160,128,$time_to_image,$water_mark,true);      
            if($thumbnail_status)
            {
                echo "Thumbnail generated";
            }
            else
            {
                echo "thumbnail generation has failed";
            }
        }
    }

}
