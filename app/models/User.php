<?php

class User extends Eloquent {
	protected $guarded = array();
	protected $table = 'users';

	public function category()
	{
		return $this->hasMany('UserCategory', 'user_id');
	}

}