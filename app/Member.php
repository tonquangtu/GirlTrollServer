<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model {

	/**
	 * Table name
	 * @var string
	 */
	protected $table='member';

	/**
	 * Attribute to show
	 * @var array
	 */
	protected $fillable=['id','member_id', 'username','rank','like','avatar_url','total_image'];

	public $timestamps = false;
	public function feed(){
		return $this->hasMany('App\Feed');
	}

	public function userevent(){
		return $this->hasMany('App\UserEvent');
	}

	public function memberlikefeed(){
		return $this->hasMany('App\MemberLikeFeed');
	}

	public function memberlikecomment(){
		return $this->hasMany('App\MemberLikeComment');
	}

	public function comment(){
		return $this->hasMany('App\Comment');
	}
}
