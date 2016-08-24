<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model {

	protected $table="video";
	protected $fillable = ['id','url_video','type','url_image_thumbnail','feed_id'];

	public $timestamps = false;
	public function feed(){
		return $this->belongsTo('App\Feed');
	}
}
