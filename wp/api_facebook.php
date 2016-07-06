<?php

require_once __DIR__ . '/facebook/src/Facebook/autoload.php';

$facebook_page_id = '161758110566235';
$facebook_page_name = 'neighborhood';
$facebook_user_id = '';

function get_facebook_config() {
	global $wpdb;
	$domain = $_SERVER['SERVER_NAME'];

	$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
	$fb_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_app_id" ';
	$fb_app_secret_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_app_secret" ';
	$fb_app_token_query .= $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_app_token" ';
	$fb_page_token_query .= $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_page_token" ';

	$fb_app_id = $wpdb->get_var( $fb_app_id_query );
	$fb_app_secret = $wpdb->get_var( $fb_app_secret_query );
	$fb_app_token = $wpdb->get_var( $fb_app_token_query );
	$fb_page_token = $wpdb->get_var( $fb_page_token_query );

	$fb_app_id = ($fb_app_id) ? $fb_app_id : null;
	$fb_app_secret = ($fb_app_secret) ? $fb_app_secret : null;

	return array(
		'app_id' => $fb_app_id,
		'app_secret' => $fb_app_secret,
		'access_token' => $fb_app_token,
		'page_access_token' => $fb_page_token
	);
}

function get_fb_variable() {

	$config = get_facebook_config();

	$fb = new Facebook\Facebook([
		'app_id' => $config['app_id'],
		'app_secret' => $config['app_secret'],
		'default_graph_version' => 'v2.4',
	]);

	return $fb;
}

function set_longlived_token() {
	$config = get_facebook_config();

	$fb = get_fb_variable();

	$helper = $fb->getRedirectLoginHelper();

	try {
		$accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	if (! isset($accessToken)) {
		if ($helper->getError()) {
			header('HTTP/1.0 401 Unauthorized');
			echo "Error: " . $helper->getError() . "\n";
			echo "Error Code: " . $helper->getErrorCode() . "\n";
			echo "Error Reason: " . $helper->getErrorReason() . "\n";
			echo "Error Description: " . $helper->getErrorDescription() . "\n";
		} else {
			header('HTTP/1.0 400 Bad Request');
			echo 'Bad request';
		}
		exit;
	}

	// Logged in
	// echo '<h3>Access Token</h3>';
	// var_dump($accessToken->getValue());

	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb->getOAuth2Client();

	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);
	// echo '<h3>Metadata</h3>';
	// var_dump($tokenMetadata);

	// Validation (these will throw FacebookSDKException's when they fail)
	$tokenMetadata->validateAppId($config['app_id']);
	// If you know the user ID this access token belongs to, you can validate it here
	// $tokenMetadata->validateUserId('123');
	// $tokenMetadata->validateExpiration();

	if (! $accessToken->isLongLived()) {
		// Exchanges a short-lived access token for a long-lived one
		try {
			$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>";
			exit;
		}
		// echo '<h3>Long-lived</h3>';
		// var_dump($accessToken->getValue());

		// $accessToken->getValue()
		update_field('sdo_api_facebook_app_token', $accessToken->getValue(), 'options');
	}

	update_field('sdo_api_facebook_app_token', $accessToken->getValue(), 'options');

	$_SESSION['fb_access_token'] = (string) $accessToken;

	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$url_to_redirect_to = $protocol.$_SERVER['SERVER_NAME'];

	header('Location: '.$url_to_redirect_to.'/facebook?fb_auth=get_page');
	exit;
}

function request_page_token() {
	global $facebook_page_id;
	$config = get_facebook_config();

	$fbApp = new Facebook\FacebookApp($config['app_id'], $config['app_secret']);
	$request = new Facebook\FacebookRequest($fbApp, $config['access_token'], 'GET', '/me/accounts');

	try {
		$fbClient = new Facebook\FacebookClient();

		$response = $fbClient->sendRequest($request);

		$graphEdge = $response->getGraphEdge();

		// Iterate over all the GraphNode's returned from the edge
		foreach ($graphEdge as $graphNode) {
			if ($graphNode['id'] === $facebook_page_id) {
				update_field('sdo_api_facebook_page_token', $graphNode['access_token'], 'options');
			}
		}

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url_to_redirect_to = $protocol.$_SERVER['SERVER_NAME'];

		header('Location: '.$url_to_redirect_to.'/wp-admin/admin.php?page=acf-options-apis');
		exit;

	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
}

function get_facebook_user($facebook_user_id) {
	$config = get_facebook_config();

	$fbApp = new Facebook\FacebookApp($config['app_id'], $config['app_secret']);
	$request = new Facebook\FacebookRequest($fbApp, $config['access_token'], 'GET', '/'.$facebook_user_id.'?fields=cover');

	try {
		$fbClient = new Facebook\FacebookClient();

		$response = $fbClient->sendRequest($request);

		return $response->getGraphNode()->asArray();

	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
}

function debug_facebook_graph_token() {
	$config = get_facebook_config();

	$fbApp = new Facebook\FacebookApp($config['app_id'], $config['app_secret']);
	$request = new Facebook\FacebookRequest($fbApp, $config['access_token'], 'GET', '/debug_token?input_token='.$config['access_token']);

	try {
		$fbClient = new Facebook\FacebookClient();

		$response = $fbClient->sendRequest($request);

		return $response->getGraphNode()->asArray();

	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		throw $e;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		throw $e;
	}
}
