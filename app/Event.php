<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

	protected $table="event";
	protected $fillable = ['eventId','title','sortContent','content','type','policy','active','timeStart','timeEnd'];

	public function userevent(){
		return $this->hasMany('App\UserEvent');
	}
}
