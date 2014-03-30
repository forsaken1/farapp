<?php

class GetController extends BaseController {

	public function get()
	{
		$ids = array();
		foreach (User::where('devise_id', 'test')->first()->category as $category)
		{
			$ids[] = $category->category_id;
		}

		if (count($ids) <= 0)
		{
			return Response::json(array('message' => 'User not select categories'));
		}

		return Response::json(array(
			'time' => Carbon\Carbon::now(),
			'isems' => Stack::whereIn('category_id', $ids)->where('created_at', '<=', '0000-00-00 00:00:00')->get()->toArray()
		));
	}
}