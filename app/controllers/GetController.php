<?php

class GetController extends BaseController {

	public function get()
	{
		if( ! Request::isJson()) return Response::json(array('message' => 'Bad headers: not json'));

		$input = Input::get();

		if (strlen($input['time']) <= 0) return Response::json(array('message' => 'Not set time'));

		$ids = array();
		foreach (User::where('devise_id', $input['register_id'])->first()->category as $category)
		{
			$ids[] = $category->category_id;
		}

		return Response::json(array(
			'time' => Carbon\Carbon::now()->toDateTimeString(),
			'items' => (count($ids) > 0) ? Stack::whereIn('category_id', $ids)->where('created_at', '>=', $input['time'])->take(50)->get()->toArray() : array(),
		));
	}
	
	public function getPostInfo()
	{
		$input=Input::get();
		return Response::json(Parser::getPostInfo($input['post']));
	}
	
}
