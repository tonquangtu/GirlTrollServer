<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model {

	protected $table="feed";
	protected $fillable=['id','title','school','time','like','comment','share','vote','member_id','checked'];
	public $timestamps = false;

	public function image(){
		return $this->hasMany('App\Image');
	}

	public function video(){
		return $this->hasOne('App\Video');
	}

	public function member(){
		return $this->belongsTo('App\Member');
	}

	public function memberlikefeed(){
		return $this->hasMany('App\MemberLikeFeed');
	}

	public function comment(){
		return $this->hasMany('App\Comment');
	}

	public function hotfeed(){
		return $this->hasOne('App\HotFeed');
	}
	
}
