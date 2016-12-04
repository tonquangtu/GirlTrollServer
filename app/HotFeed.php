<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class HotFeed extends Model {

	protected $table="hot_feed";
	protected $fillable=['id','feed_id','type'];
	public $timestamps = false;

	public function feed(){
		return $this->belongsTo('App\Feed');
	}
	
}
