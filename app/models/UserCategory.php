<?php

class UserCategory extends Eloquent {
	protected $guarded = array();
	protected $table = 'user_category';

	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}

	public function category()
	{
		return $this->belongsTo('Category', 'category_id');
	}
}