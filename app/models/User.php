<?php

class User extends Eloquent {
	protected $guarded = array();
	protected $table = 'users';

	public function category()
	{
		return $this->hasMany('UserCategory', 'user_id');
	}

	public function scopeCategoryId($query, $category_id)
	{
		return $query->where('category_id', $category_id)->get();
	}

}