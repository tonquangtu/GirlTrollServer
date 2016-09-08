<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberLikeComment extends Model {

	protected $table="member_like_comment";
	protected $fillable = ['id', 'member_id', 'comment_id','is_like'];

	public $timestamps = false;

	public function member(){
		return $this->belongsTo('App\Member');
	}

	public function comment(){
		return $this->belongsTo('App\Comment');
	}
}
