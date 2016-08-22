<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model {

	protected $table="feed";
	protected $fillable=['id','title','school','time','like','comment','share','vote','member_id'];
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
}
