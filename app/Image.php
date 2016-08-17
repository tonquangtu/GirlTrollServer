<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model {

	protected $table="image";
	protected $fillable=['id','url_image','type','link_face','feed_id','url_image_thumbnail'];
	public $timestamps = false;
	public function feed(){
		return $this->belongsTo('App\Feed');
	}

	public function userevent(){
		return $this->hasMany('App\UserEvent');
	}
}
