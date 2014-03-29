<?php

class PushController extends BaseController {

	public function test()
	{
		$client = new Google_Client();
		$client->setApplicationName("FarApp");
		$client->setDeveloperKey("AIzaSyDqnS3844V6eACSFjQpFW1ngzakRmZ4pP4");
		$client->setClientId('617404061855.apps.googleusercontent.com');
		$client->setClientSecret('BmKlhDHClYCS8g-PKTZ_uelz');
		$client->setRedirectUri('/');

		echo 'success auth';
	}

	public function auth()
	{
		if (isset($_GET['code'])) {
			$client->authenticate();
			$_SESSION['token'] = $client->getAccessToken();
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		}
	}
}