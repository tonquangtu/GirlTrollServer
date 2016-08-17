<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model {

	protected $table="video";
	protected $fillable = ['videoId','urlVideo','type','feedId'];

	public function feed(){
		return $this->belongsTo('App\Feed');
	}
}
