<?php

class User extends Eloquent {
	protected $guarded = array();

	public function category()
	{
		return $this->hasMany('Category', 'user_id');
	}

}