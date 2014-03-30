<?php

class RegisterController extends BaseController {

	private static $error_message = array('message' => 'Error: bad json', 'result' => 0);
	private static $success_message = array('message' => 'OK', 'result' => 1);

	public function register()
	{
		if(!Request::isJson())
		{
			return Response::json(array('message' => 'Bad headers: not json'));
		}

		$input = Input::json();
		#$input = self::getValidData($input);

		if(!$input)
		{
			return Response::json(self::$error_message);
		}
		$user = User::firstOrNew(array('devise_id' => $input->register_id));
		$user->save();
		UserCategory::where('user_id', $user->id)->delete();

		foreach($input->category as $cat)
		{
			UserCategory::create(array('user_id' => $user->id, 'category_id' => $cat));
		}
		return Response::json($success_message);
	}

	private static function getValidData($data)
	{
		$result = json_decode($data, true);

		if(!isset($result['register_id']) || !isset($result['category']))
			return false;

		return $result;
	}
}