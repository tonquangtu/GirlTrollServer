<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model {

	protected $table="image";
	protected $fillable=['imageId','urlImage','type','linkFace','feedId','urlImageThumbnail'];

	public function feed(){
		return $this->belongsTo('App\Feed');
	}

	public function userevent(){
		return $this->hasMany('App\UserEvent');
	}
}
