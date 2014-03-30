<?php

class PushController extends BaseController {

	private static $url = 'https://android.googleapis.com/gcm/send';
	private static $google_api_key = 'AIzaSyDqnS3844V6eACSFjQpFW1ngzakRmZ4pP4';

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
		$client = new Google_Client();
		$client->setApplicationName("FarApp");
		$client->setDeveloperKey("AIzaSyDqnS3844V6eACSFjQpFW1ngzakRmZ4pP4");
		$client->setClientId('617404061855.apps.googleusercontent.com');
		$client->setClientSecret('BmKlhDHClYCS8g-PKTZ_uelz');
		$client->setRedirectUri('/');

		$test_registration_id = 'APA91bGsb0nWZaQmSu9C6G2xlkZTgPBmNcRxtdoFkd7uxjcqcsy97kUU42uEZync_j9cM_VS96bJdLP0YSd7iQZAwjit58zs3KzV-FCpHdTxO4V4dD_HoFM8wKN3895zLX6xhOJTigkClDDWWB_2BhA0_RWK6IRQMg';
		$test_text = 'hello, android';

		echo $this->sendPushNotificationToGCM(
			array($test_registration_id),
			array('message' => $test_text)
		);
	}

	public function push()
	{
		$new_auto_count = 0;
		$new_flat_count = 0;
		$new_job_count  = 0;
		$new_free_count = 0;

		# Parsing
		$auto = Parser::getPosts('auto/sale', 1, 2);
		$flat = Parser::getPosts('realty/sell_flats', 2, 2);
		$job  = Parser::getPosts('auto/sale', 3, 2);
		$free = Parser::getPosts('free', 4, 2);

		$auto_old = Stack::where('category_id', 1)->get()->lists('id', 'key');
		$flat_old = Stack::where('category_id', 2)->get()->lists('id', 'key');
		$job_old  = Stack::where('category_id', 3)->get()->lists('id', 'key');
		$free_old = Stack::where('category_id', 4)->get()->lists('id', 'key');

		foreach($auto as $item)
		{
			if(isset($auto_old[ $item['key'] ]) && $auto_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_auto_count++;
		}

		foreach($flat as $item)
		{
			if(isset($flat_old[ $item['key'] ]) && $flat_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_flat_count++;
		}

		foreach($job as $item)
		{
			if(isset($job_old[ $item['key'] ]) && $job_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_job_count++;
		}

		foreach($free as $item)
		{
			if(isset($free_old[ $item['key'] ]) && $free_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_free_count++;
		}

		# Send-to-mobile-apps
		$users = User::all();

		foreach($users as $user)
		{
			$category = UserCategory::where('user_id', $user->id)->lists('id', 'id');
			$message = 'Новые объявления на Farpost: ';
			isset($category[1]) && $new_auto_count && $message .= $new_auto_count.' автомобилей ';
			isset($category[2]) && $new_flat_count && $message .= $new_flat_count.' квартир ';
			isset($category[3]) && $new_job_count  && $message .= $new_job_count. ' вакансий ';
			isset($category[4]) && $new_free_count && $message .= $new_free_count.' бесплатных вещей ';

			$this->sendPushNotificationToGCM(
				array($user->device_id),
				array('message' => $message)
			);
		}
		echo 'ok';
	}
}