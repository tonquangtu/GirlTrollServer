<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageCover extends Model {

	protected $table="image_cover";
	protected $fillable = ['id','title','url_image'];
	public $timestamps = false;
}
