<?php

class AdminController extends BaseController {

	public function users()
	{
		echo User::all();
	}


	public function category()
	{
		echo Category::all();
	}


	public function user_category()
	{
		echo UserCategory::all();
	}


	public function stack()
	{
		echo Stack::all();
	}
}