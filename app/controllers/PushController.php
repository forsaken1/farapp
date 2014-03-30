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

	public function pushAuto()
	{
		$new_auto_count = 0;
		$auto = Parser::getPosts('auto/sale', 1, 5);
		$auto_old = Stack::where('category_id', 1)->get()->lists('id', 'key');

		foreach($auto as $item)
		{
			if(isset($auto_old[ $item['key'] ]) && $auto_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_auto_count++;
		}

		$users = User::all();

		foreach($users as $user)
		{
			$category = UserCategory::where('user_id', $user->id)->lists('category_id', 'category_id');
			$message = 'Новые объявления на Farpost: ';
			isset($category[1]) && $new_auto_count && $message .= $new_auto_count.' автомобилей';

			$this->sendPushNotificationToGCM(
				array($user->devise_id),
				array('message' => $message)
			);
		}
		echo 'ok';
	}

	public function pushFlat()
	{
		$new_flat_count = 0;
		$flat = Parser::getPosts('realty/sell_flats', 2, 5);
		$flat_old = Stack::where('category_id', 2)->get()->lists('id', 'key');
		
		foreach($flat as $item)
		{
			if(isset($flat_old[ $item['key'] ]) && $flat_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_flat_count++;
		}

		$users = User::all();

		foreach($users as $user)
		{
			$category = UserCategory::where('user_id', $user->id)->lists('category_id', 'category_id');
			$message = 'Новые объявления на Farpost: ';
			isset($category[2]) && $new_flat_count && $message .= $new_flat_count.' квартир ';

			$this->sendPushNotificationToGCM(
				array($user->devise_id),
				array('message' => $message)
			);
		}
		echo 'ok';
	}

	public function pushJob()
	{
		$new_job_count  = 0;
		$job  = Parser::getPosts('/job/vacancy/+/IT+-+%D2%E5%EB%E5%EA%EE%EC/', 3, 5);
		$job_old  = Stack::where('category_id', 3)->get()->lists('id', 'key');
		
		foreach($job as $item)
		{
			if(isset($job_old[ $item['key'] ]) && $job_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_job_count++;
		}

		$users = User::all();

		foreach($users as $user)
		{
			$category = UserCategory::where('user_id', $user->id)->lists('category_id', 'category_id');
			$message = 'Новые объявления на Farpost: ';
			isset($category[3]) && $new_job_count  && $message .= $new_job_count. ' вакансий ';

			$this->sendPushNotificationToGCM(
				array($user->devise_id),
				array('message' => $message)
			);
		}
		echo 'ok';
	}

	public function pushFree()
	{
		$new_free_count = 0;
		$free = Parser::getPosts('free', 4, 5);
		$free_old = Stack::where('category_id', 4)->get()->lists('id', 'key');

		foreach($free as $item)
		{
			if(isset($free_old[ $item['key'] ]) && $free_old[ $item['key'] ])
				continue;
			Stack::create($item);
			$new_free_count++;
		}

		$users = User::all();

		foreach($users as $user)
		{
			$category = UserCategory::where('user_id', $user->id)->lists('category_id', 'category_id');
			$message = 'Новые объявления на Farpost: ';
			isset($category[4]) && $new_free_count && $message .= $new_free_count.' бесплатных вещей ';

			$this->sendPushNotificationToGCM(
				array($user->devise_id),
				array('message' => $message)
			);
		}
		echo 'ok';
	}
}