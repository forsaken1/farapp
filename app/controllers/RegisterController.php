<?php

class RegisterController extends BaseController {

	private static $error_message = array('message' => 'Error: bad json');

	public function register()
	{
		$input = Input::all();

		if(Request::isMethod('get'))
		{
			$input = '{"category":[1,2,3],"register_id":"reg_id"}';
		}

		$input = self::getValidData($input);

		if(!$input)
		{
			return json_encode(self::$error_message);
		}
		$user = User::firstOfCreate(array('devise_id' => $input['register_id']));
		UserCategory::where('user_id', $user->id)->delete();

		foreach($input['category'] as $cat)
		{
			UserCategory::create(array('user_id' => $user->id, 'category_id' => $cat));
		}
	}

	private function getValidData($data)
	{
		$result = json_decode($date, true);

		if(!isset($result['register_id']) || !isset($result['category']))
			return false;

		return $result;
	}
}