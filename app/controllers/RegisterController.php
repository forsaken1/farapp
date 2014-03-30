<?php

class RegisterController extends BaseController {

	private static $error_message = array('message' => 'Error: bad json', 'result' => 0);
	private static $success_message = array('message' => 'OK', 'result' => 1);

	public function register()
	{
		$input = Input::all();

		if(Request::isMethod('get'))
		{
			$input = "{\n \"register_id\": \"APA91bGsb0nWZaQmSu9C6G2xlkZTgPBmNcRxtdoFkd7uxjcqcsy97kUU42uEZync_j9cM_VS96bJdLP0YSd7iQZAwjit58zs3KzV-FCpHdTxO4V4dD_HoFM8wKN3895zLX6xhOJTigkClDDWWB_2BhA0_RWK6IRQMg\",\n \"category\": [\n 1,\n 2\n ]\n}";
		}

		$input = self::getValidData($input);

		if(!$input)
		{
			echo json_encode(self::$error_message);
			return;
		}
		$user = User::firstOrNew(array('devise_id' => $input['register_id']));
		$user->save();
		UserCategory::where('user_id', $user->id)->delete();

		foreach($input['category'] as $cat)
		{
			UserCategory::create(array('user_id' => $user->id, 'category_id' => $cat));
		}
		echo json_encode(self::$success_message);
	}

	private static function getValidData($data)
	{
		$result = json_decode($data, true);

		if(!isset($result['register_id']) || !isset($result['category']))
			return false;

		return $result;
	}
}