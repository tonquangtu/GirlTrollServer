<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	protected $table="comment";
	protected $fillable=['id','member_id','feed_id','comment', 'num_like'];

	public $timestamps = false;

	public function member(){
		return $this->belongsTo('App\Member');
	}

	public function feed(){
		return $this->belongsTo('App\Feed');
	}

	public function memberlikecomment(){
		return $this->hasMany('App\MemberLikeComment');
	}

}
