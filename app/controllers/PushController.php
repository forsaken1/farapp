<?php

class PushController extends BaseController {

	private static $url = 'https://android.googleapis.com/gcm/send';
	private static $google_api_key = 'AIzaSyDqnS3844V6eACSFjQpFW1ngzakRmZ4pP4';

	private function sendPushNotificationToGCM($registation_ids, $message)
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
}