<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

	protected $table="event";
	protected $fillable = ['id','title','sort_content','content','type','policy','active','time_start','time_end'];
	public $timestamps = false;
	
	public function userevent(){
		return $this->hasMany('App\UserEvent');
	}
}
