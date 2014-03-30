<?php

class Category extends Eloquent {
	protected $guarded = array();

	public function stack()
	{
		return $this->hasMany('Stack', 'category_id');
	}
	
}