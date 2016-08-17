<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model {

	protected $table="user_event";
	protected $fillable=['id','member_id','image_id', 'event_id'];

	public $timestamps = false;
	public function member(){
		return $this->belongsTo('App\Member');
	}

	public function image(){
		return $this->belongsTo('App\Image');
	}

	public function event(){
		return $this->belongsTo('App\Event');
	}
}
