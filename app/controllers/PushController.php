<?php

class PushController extends BaseController {

	private static $url = 'https://android.googleapis.com/gcm/send';
	private static $google_api_key = 'AIzaSyDqnS3844V6eACSFjQpFW1ngzakRmZ4pP4';
	private static $pages_count = 4;
	private static $category_names = array('', 'автомобилей', 'квартир', 'вакансий', 'бесплатных');

	private static function sendPushNotificationToGCM($registation_ids, $message)
	{
		$fields = array(
			'registration_ids' => $registation_ids,
			'data' => $message,
		);
		$headers = array(
			'Authorization: key='.self::$google_api_key,
			'Content-Type: application/json'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);

		if ($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		}
		curl_close($ch);

		return $result;
	}

	public function test()
	{
		$users = User::all();

		foreach($users as $user)
		{
			$test_text = 'hello, android';

			echo $this->sendPushNotificationToGCM(
				array($user->devise_id),
				array('message' => $test_text)
			);
			echo 'ok'.$user->devise_id;
		}
	}

	public static function push($url, $category_id)
	{
		$count = 0;
		$parsed = Parser::getPosts($url, $category_id, self::$pages_count);
		$parsed_old = Stack::where('category_id', $category_id)->get()->lists('id', 'key');

		foreach($parsed as $item)
		{
			if(isset($parsed_old[ $item['key'] ]) && $parsed_old[ $item['key'] ])
				continue;

			Stack::create($item);
			$count++;
		}

		$users = User::all();
		$sended_message_count = 0;

		if($count)
		{
			foreach($users as $user)
			{
				$category = UserCategory::where('user_id', $user->id)->lists('category_id', 'category_id');
				$message = "Новые объявления на Farpost: $count ".self::$category_names[$category_id];

				self::sendPushNotificationToGCM(
					array($user->devise_id),
					array('message' => $message)
				);
				$sended_message_count++;
			}
		}
		echo "New adds count: $count";
		echo "\nSended message count: $sended_message_count";
		echo "\n\n";
	}

	public function pushAuto()
	{
		self::push('auto/sale', 1);
	}

	public function pushFlat()
	{
		self::push('realty/sell_flats', 2);
	}

	public function pushJob()
	{
		self::push('/job/vacancy/+/IT+-+%D2%E5%EB%E5%EA%EE%EC/', 3);
	}

	public function pushFree()
	{
		self::push('free', 4);
	}
}