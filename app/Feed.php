<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model {

	protected $table="feed";
	protected $fillable=['feedId','title','school','time','like','comment','share','memberId'];

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
