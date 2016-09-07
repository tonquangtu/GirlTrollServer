<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberLikeFeed extends Model {

	protected $table="member_like_feed";
	protected $fillable=['id','member_id','feed_id','is_like'];

	public $timestamps = false;

	public function member(){
		return $this->belongsTo('App\Member');
	}

	public function feed(){
		return $this->belongsTo('App\Feed');
	}

}
