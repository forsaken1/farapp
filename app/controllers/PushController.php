<?php

class PushController extends BaseController {

	function sendPushNotificationToGCM($registation_ids, $message)
	{
		$url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
			'registration_ids' => $registation_ids,
			'data' => $message,
		);
		define("GOOGLE_API_KEY", "AIzaSyDqnS3844V6eACSFjQpFW1ngzakRmZ4pP4");   
		$headers = array(
			'Authorization: key=' . GOOGLE_API_KEY,
			'Content-Type: application/json'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
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

		echo $this->sendPushNotificationToGCM(
			array('APA91bGsb0nWZaQmSu9C6G2xlkZTgPBmNcRxtdoFkd7uxjcqcsy97kUU42uEZync_j9cM_VS96bJdLP0YSd7iQZAwjit58zs3KzV-FCpHdTxO4V4dD_HoFM8wKN3895zLX6xhOJTigkClDDWWB_2BhA0_RWK6IRQMg'), 
			array('message' => 'hello, android')
		);
	}
}