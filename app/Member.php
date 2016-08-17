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
	protected $fillable=['memberId', 'username','rank','like','avatarUrl','totalImage'];

	public function feed(){
		return $this->hasMany('App\Feed');
	}

	public function userevent(){
		return $this->hasMany('App\UserEvent');
	}
}
