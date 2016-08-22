<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageEvent extends Model {

	protected $table = "image_event";
	protected $fillable = ['id','image_id','event_id'];

	public $timeStart = false;

	public function image(){
		return $this->belongsTo('image');
	}

	public function event(){
		return $this->belongsTo('event');
	}

}
