<?php
use Carbon\Carbon as Carbon;

if ( is_user_logged_in( ) ) {
	session_start();
} else {
	if (isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID']) {
		unset($_COOKIE['PHPSESSID']);
		setcookie('PHPSESSID', '', time() - 3600, '/');
	}
}

// This logic is to grab the facebook short/long-lived tokens and put the into the database, then redirect the user to the admin dashboard.
if (substr($_SERVER['REQUEST_URI'], 0, 9) === '/facebook') {
	if ( $_GET['code'] && $_GET['state'] ) {
		set_longlived_token();
	}

	if ($_GET['fb_auth'] === 'get_page') {
		request_page_token();
	}
} else if (substr($_SERVER['REQUEST_URI'], 0, 6) === '/vimeo') {
	if ( $_GET['code'] && $_GET['state'] ) {
		set_longlived_vimeo_token();
	}
}

$api_not_added_emoji = ' - 😴';
$api_added_not_setup_emoji = ' - 🤔';
$api_setup_emoji = ' - 😄';

$facebook_enabled = 0;
$facebook_message = 'Looks like you don\'t have Facebook\'s API enabled! you can turn this on by uncommenting the <code style="display:inline">wp/api_facebook.php</code> file in <code style="display:inline">functions.php</code>';

$twitter_enabled = 0;
$twitter_message = 'Looks like you don\'t have Twitter\'s API enabled! you can turn this on by uncommenting the <code style="display:inline">wp/api_twitter.php</code> file in <code style="display:inline">functions.php</code>';

$vimeo_enabled = 0;
$vimeo_message = 'Looks like you don\'t have Vimeo\'s API enabled! you can turn this on by uncommenting the <code style="display:inline">wp/api_vimeo.php</code> file in <code style="display:inline">functions.php</code>';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

if ( is_user_logged_in( ) ) {
	global $wpdb;

	$facebook_tab_title = 'Facebook API' . $api_not_added_emoji;
	$twitter_tab_title = 'Twitter API' . $api_not_added_emoji;
	$instagram_tab_title = 'Instagram API' . $api_not_added_emoji;
	$vimeo_tab_title = 'Vimeo API' . $api_not_added_emoji;
	$dribbble_tab_title = 'Dribbble API' . $api_not_added_emoji;

	if ( isset($_GET['page']) && $_GET['page'] === 'acf-options-apis' ) {

		/* FACEBOOK AUTHENTICATION */
		if (function_exists('get_facebook_config')) {
			$facebook_tab_title = 'Facebook API' . $api_added_not_setup_emoji;
			$facebook_enabled = 1;
			$facebook_message = 'Use this for pulling in your latest posts on the site somewhere<br /> <br /> <b>Step 1.</b> %step_1_content% <br /> <br /> <b>Step 2.</b> %step_2_content%<br /> <br /> <b>Step 3.</b> %step_3_content%';
			$fb_login_copy_step_one = 'Create an app <a href="https://developers.facebook.com/quickstarts/?platform=web" target="_blank">here</a> and fill in the details below.';
			$fb_login_copy_step_two = 'If you need a new token, <a href="%login_url%">log in with Facebook!</a>';
			$fb_login_copy_step_three = 'Done! You can use Facebook in your code now. ';

			$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
			$fb_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_app_id" ';
			$fb_app_secret_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_app_secret" ';
			$fb_app_token_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_app_token" ';
			$fb_page_token_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_facebook_page_token" ';

			$fb_app_id = $wpdb->get_var( $fb_app_id_query );
			$fb_app_secret = $wpdb->get_var( $fb_app_secret_query );
			$fb_app_token = $wpdb->get_var( $fb_app_token_query );
			$fb_page_token = $wpdb->get_var( $fb_page_token_query );

			if ($fb_app_id !== null && $fb_app_secret !== null && $fb_app_token !== null) {
				$facebook_message = str_replace('%step_1_content%', '<del>%step_1_content%</del>', $facebook_message);
			}

			if ($fb_app_id !== null && $fb_app_secret !== null) {
				$fb = get_fb_variable();
				$fb_helper = $fb->getRedirectLoginHelper();
				$fb_permissions = ['email', 'manage_pages'];
				$fb_loginUrl = $fb_helper->getLoginUrl($protocol.$_SERVER['SERVER_NAME'].'/facebook', $fb_permissions);

				$fb_loginUrl = htmlspecialchars($fb_loginUrl);
				$facebook_message = str_replace('%step_1_content%', '<del>'.$fb_login_copy_step_one.'<del>', $facebook_message);
			} else {
				$fb_loginUrl = "#";
				$facebook_message = str_replace('%step_1_content%', $fb_login_copy_step_one, $facebook_message);
				$facebook_message = str_replace('%step_2_content%', '<span style="opacity: 0.25;">%step_2_content%</span>', $facebook_message);
			}

			if ($fb_app_token == null && $fb_page_token == null) {
				$facebook_message = str_replace('%step_3_content%', '<span style="opacity: 0.25;">%step_3_content%</span>', $facebook_message);
			} else {
				$facebook_message = str_replace('%step_2_content%', '<del>%step_2_content%</del>', $facebook_message);

				$facebook_message = str_replace('%step_3_content%', '%step_3_content% This token will expire on <strong>%token_expiration_date%</strong>. Renew it by <a href="'.$fb_loginUrl.'">clicking here</a>. ', $facebook_message);

				$debugged_token = debug_facebook_graph_token();

				$fb_expires_at = new Carbon( $debugged_token['expires_at']->format('r') );

				$facebook_message = str_replace('%token_expiration_date%', $fb_expires_at, $facebook_message);
				$facebook_tab_title = 'Facebook API' . $api_setup_emoji;
			}

			$fb_login_copy_step_two = str_replace('%login_url%', $fb_loginUrl, $fb_login_copy_step_two);
			$facebook_message = str_replace('%step_2_content%', $fb_login_copy_step_two, $facebook_message);
			$facebook_message = str_replace('%step_3_content%', $fb_login_copy_step_three, $facebook_message);
		}

		/* TWITTER AUTHENTICATION */
		if (function_exists('get_recent_tweet_by_username')) {
			$twitter_tab_title = 'Twitter API' . $api_added_not_setup_emoji;
			$twitter_enabled = 1;
			$twitter_message = 'Use this for pulling in your latest posts on the site somewhere. <br /> <br /> <b>Step 1.</b> %step_1_content% <br /> <br /> <b>Step 2.</b> %step_2_content%<br /> <br /> <b>Step 3.</b> %step_3_content%';

			$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
			$tw_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_twitter_app_id" ';
			$tw_app_secret_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_twitter_app_consumer_key" ';
			$tw_app_token_query .= $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_twitter_app_consumer_secret" ';
			$tw_access_token_query .= $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_twitter_app_access_token" ';
			$tw_access_token_secret_query .= $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_twitter_app_access_token_secret" ';

			$tw_app_id = $wpdb->get_var( $tw_app_id_query );
			$tw_app_secret = $wpdb->get_var( $tw_app_secret_query );
			$tw_app_token = $wpdb->get_var( $tw_app_token_query );
			$tw_access_token = $wpdb->get_var( $tw_access_token_query );
			$tw_access_token_secret = $wpdb->get_var( $tw_access_token_secret_query );

			if ($tw_app_id !== null && $tw_app_secret !== null && $tw_app_token !== null) {
				$twitter_message = str_replace('%step_1_content%', '<del>%step_1_content%</del>', $twitter_message);
			}

		// Step 1 content - Create an app link
			$step_one_content = 'Create an app <a href="https://apps.twitter.com/" target="_blank">here</a> and fill in the App ID, Consumer Key, and Consumer Secret below.';

			$twitter_message = str_replace('%step_1_content%', $step_one_content, $twitter_message);

			if ( $tw_access_token !== null && $tw_access_token_secret !== null ) {
				$twitter_message = str_replace('%step_2_content%', '<del>%step_2_content%</del>', $twitter_message);
				$twitter_tab_title = 'Twitter API' . $api_setup_emoji;
			}

			$step_two_content = ($tw_app_id !== null) ? 'Go <a href="https://apps.twitter.com/app/'.$tw_app_id.'/keys" target="_blank">here</a>, generate an Access Token, and fill in the Access Token, and Access Token Secret fields below.' : '<span style="opacity:0.25">Go here, generate an Access Token, and fill in the Access Token, and Access Token Secret fields below.</span>';

			$twitter_message = str_replace('%step_2_content%', $step_two_content, $twitter_message);

		// Step 3 content - Create an app link
			$step_three_content = ($tw_access_token_secret !== null) ? 'Done! You can use Twitter in your code now.' : '<span style="opacity: 0.25">Done!</span>';

			$twitter_message = str_replace('%step_3_content%', $step_three_content, $twitter_message);
		}

		/* VIMEO AUTHENTICATION */
		if (function_exists('get_vimeo_config')) {
			$vimeo_tab_title = 'Vimeo API' . $api_added_not_setup_emoji;
			$vimeo_enabled = 1;
			$vimeo_message = 'Use this for pulling in branded vimeo videos<br /> <br /> <b>Step 1.</b> %step_1_content% <br /> <br /> <b>Step 2.</b> %step_2_content%<br /> <br /> <b>Step 3.</b> %step_3_content%';
			$vimeo_login_copy_step_one = 'Create an app <a href="https://developer.vimeo.com/apps/new" target="_blank">here</a> and fill in the details below.';
			$vimeo_login_copy_step_two = 'If you need a new token, <a href="%login_url%">log in with Vimeo!</a>';
			$vimeo_login_copy_step_three = 'Done! You can use Vimeo in your code now. ';

			$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
			$vimeo_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_app_id" ';
			$vimeo_app_secret_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_app_secret" ';
			$vimeo_app_token_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_app_token" ';
			$vimeo_page_token_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_page_token" ';

			$vimeo_app_id = $wpdb->get_var( $vimeo_app_id_query );
			$vimeo_app_secret = $wpdb->get_var( $vimeo_app_secret_query );
			$vimeo_app_token = $wpdb->get_var( $vimeo_app_token_query );
			$vimeo_page_token = $wpdb->get_var( $vimeo_page_token_query );

			if ($vimeo_app_id !== null && $vimeo_app_secret !== null && $vimeo_app_token !== null) {
				$vimeo_message = str_replace('%step_1_content%', '<del>%step_1_content%</del>', $vimeo_message);
			}

			if ($vimeo_app_id !== null && $vimeo_app_secret !== null) {
				$vimeo = get_vimeo_variable();
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$scopes = ['public', 'private'];
				$state = base64_encode(openssl_random_pseudo_bytes(30));
				$vimeo_login_url = $vimeo->buildAuthorizationEndpoint($protocol.$_SERVER['SERVER_NAME'].'/vimeo', $scopes, $state);
				$vimeo_message = str_replace('%step_1_content%', '<del>'.$vimeo_login_copy_step_one.'<del>', $vimeo_message);
			} else {
				$vimeo_login_url = "#";
				$vimeo_message = str_replace('%step_1_content%', $vimeo_login_copy_step_one, $vimeo_message);
				$vimeo_message = str_replace('%step_2_content%', '<span style="opacity: 0.25;">%step_2_content%</span>', $vimeo_message);
			}

			if ($vimeo_app_token == null && $vimeo_page_token == null) {
				$vimeo_message = str_replace('%step_3_content%', '<span style="opacity: 0.25;">%step_3_content%</span>', $vimeo_message);
			} else {
				$vimeo_message = str_replace('%step_2_content%', '<del>%step_2_content%</del>', $vimeo_message);

				$vimeo_message = str_replace('%step_3_content%', '%step_3_content% Renew it by <a href="'.$vimeo_login_url.'">clicking here</a>. ', $vimeo_message);
				$vimeo_tab_title = 'Vimeo API' . $api_setup_emoji;
			}


		//htmlspecialchars($vimeo_login_url)
			$vimeo_login_copy_step_two = str_replace('%login_url%', $vimeo_login_url, $vimeo_login_copy_step_two);
			$vimeo_message = str_replace('%step_2_content%', $vimeo_login_copy_step_two, $vimeo_message);
			$vimeo_message = str_replace('%step_3_content%', $vimeo_login_copy_step_three, $vimeo_message);
		}

		if (function_exists('instagram_request')) {
			$instagram_tab_title = 'Instagram API' . $api_added_not_setup_emoji;

			$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
			$instagram_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_instagram_app_id" ';

			$instagram_app_id = $wpdb->get_var( $instagram_app_id_query );

			if ( $instagram_app_id !== null ) {
				$instagram_tab_title = 'Instagram API' . $api_setup_emoji;
			}
		}

		if (function_exists('dribbble_request')) {
			$dribbble_tab_title = 'Dribbble API' . $api_added_not_setup_emoji;

			$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
			$dribbble_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_dribbble_app_id" ';

			$dribbble_app_id = $wpdb->get_var( $dribbble_app_id_query );

			if ( $dribbble_app_id !== null ) {
				$dribbble_tab_title = 'Dribbble API' . $api_setup_emoji;
			}
		}

	}


	if( function_exists('acf_add_local_field_group') ):

		acf_add_local_field_group(array (
			'key' => 'group_56fec11280215',
			'title' => 'Third party API\'s',
			'fields' => array (
				/* TWITTER */
				array (
					'key' => 'field_56fc1ea4bea5e',
					'label' => $twitter_tab_title,
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placement' => 'top',
					'endpoint' => 0,
					),
				array (
					'key' => 'field_56fc245e8e1f9',
					'label' => 'How to use this',
					'name' => '',
					'type' => 'message',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'message' => $twitter_message,
					'new_lines' => 'wpautop',
					'esc_html' => 0,
					),
				array (
					'key' => 'field_56fc24ac8e1fd',
					'label' => 'App ID',
					'name' => 'sdo_api_twitter_app_id',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				array (
					'key' => 'field_56fc24bb8e1fe',
					'label' => 'Consumer Key',
					'name' => 'sdo_api_twitter_app_consumer_key',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				array (
					'key' => 'field_56fc24c08e1ff',
					'label' => 'Consumer Secret',
					'name' => 'sdo_api_twitter_app_consumer_secret',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				array (
					'key' => 'field_56fc24cb8e200',
					'label' => 'Access Token',
					'name' => 'sdo_api_twitter_app_access_token',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				array (
					'key' => 'field_56fc24d38e201',
					'label' => 'Access Token Secret',
					'name' => 'sdo_api_twitter_app_access_token_secret',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				/* FACEBOOK */
				array (
					'key' => 'field_56fc1eaebea5f',
					'label' => $facebook_tab_title,
					'name' => '_copy',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placement' => 'top',
					'endpoint' => 0,
					),
				array (
					'key' => 'field_56fc24668e1fa',
					'label' => 'How to use this',
					'name' => '',
					'type' => 'message',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'message' => $facebook_message,
					'new_lines' => 'wpautop',
					'esc_html' => 0,
					),
				array (
					'key' => 'field_56fc25808e202',
					'label' => 'App ID',
					'name' => 'sdo_api_facebook_app_id',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => ! $facebook_enabled,
					),
				array (
					'key' => 'field_56fc25a58e203',
					'label' => 'App Secret',
					'name' => 'sdo_api_facebook_app_secret',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => ! $facebook_enabled,
					),
				array (
					'key' => 'field_56fc25bb8e204',
					'label' => 'Long Lived Token',
					'name' => 'sdo_api_facebook_app_token',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => ! $facebook_enabled,
					),
				array (
					'key' => 'field_56fc25cc8e205',
					'label' => 'Long lived page token',
					'name' => 'sdo_api_facebook_page_token',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => ! $facebook_enabled,
					),
				/* VIMEO */
				array (
					'key' => 'field_56fc1ecebea61',
					'label' => $vimeo_tab_title,
					'name' => '_copy',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placement' => 'top',
					'endpoint' => 0,
					),
				array (
					'key' => 'field_56fc246e8e1fc',
					'label' => 'How to use this',
					'name' => '',
					'type' => 'message',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'message' => $vimeo_message,
					'new_lines' => 'wpautop',
					'esc_html' => 0,
					),
				array (
					'key' => 'field_56fc2a1634b40',
					'label' => 'App ID',
					'name' => 'sdo_api_vimeo_app_id',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => ! $vimeo_enabled,
					),
				array (
					'key' => 'field_56fc2a2034b41',
					'label' => 'App Secret',
					'name' => 'sdo_api_vimeo_app_secret',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => ! $vimeo_enabled,
					),
				array (
					'key' => 'field_56fc2a3f34b42',
					'label' => 'App Token',
					'name' => 'sdo_api_vimeo_app_token',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => ! $vimeo_enabled,
					),
				array (
					'key' => 'field_56fc2a5034b43',
					'label' => 'App Long Lived Token',
					'name' => 'sdo_api_vimeo_page_token',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => ! $vimeo_enabled,
					),
				/* INSTAGRAM */
				array (
					'key' => 'field_56fc1eb7bea60',
					'label' => $instagram_tab_title,
					'name' => '_copy',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placement' => 'top',
					'endpoint' => 0,
					),
				array (
					'key' => 'field_56fc24698e1fb',
					'label' => 'How to use this',
					'name' => '',
					'type' => 'message',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'message' => 'Use this for pulling in your latest posts on the site somewhere - Create an app <a href="https://instagram.com/developer/clients/manage/">here</a> and fill in the details below. The only thing we need to retrieve Instagram posts is a valid API key.',
					'new_lines' => 'wpautop',
					'esc_html' => 0,
					),
				array (
					'key' => 'field_56fc277334b3f',
					'label' => 'App ID',
					'name' => 'sdo_api_instagram_app_id',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				/* DRIBBBLE */
				array (
					'key' => 'field_56fc2c9db8937',
					'label' => $dribbble_tab_title,
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placement' => 'top',
					'endpoint' => 0,
					),
				array (
					'key' => 'field_56fc2cccb8939',
					'label' => 'How to use this',
					'name' => '',
					'type' => 'message',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'message' => 'Use this for pulling in your latest posts on the site somewhere - Create an app <a href="https://dribbble.com/account/applications/new" target="_blank">here</a> and fill in the details below. Dribbble will give you all three fields. ',
					'new_lines' => 'wpautop',
					'esc_html' => 0,
					),
				array (
					'key' => 'field_56fc2caeb8938',
					'label' => 'App ID',
					'name' => 'sdo_api_dribbble_app_id',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				array (
					'key' => 'field_56fc2ce0b893a',
					'label' => 'App Secret',
					'name' => 'sdo_api_dribbble_app_secret',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				array (
					'key' => 'field_56fc2ceeb893b',
					'label' => 'Long lived token',
					'name' => 'sdo_api_dribbble_app_token',
					'type' => 'password',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
						),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'readonly' => 0,
					'disabled' => 0,
					),
				),
'location' => array (
	array (
		array (
			'param' => 'options_page',
			'operator' => '==',
			'value' => 'acf-options-apis',
			),
		),
	),
'menu_order' => 0,
'position' => 'acf_after_title',
'style' => 'seamless',
'label_placement' => 'top',
'instruction_placement' => 'label',
'hide_on_screen' => array (
	0 => 'permalink',
	1 => 'the_content',
	2 => 'excerpt',
	3 => 'custom_fields',
	4 => 'discussion',
	5 => 'comments',
	6 => 'revisions',
	7 => 'slug',
	8 => 'author',
	9 => 'format',
	10 => 'page_attributes',
	11 => 'featured_image',
	12 => 'categories',
	13 => 'tags',
	14 => 'send-trackbacks',
	),
'active' => 1,
'description' => 'This sets up the entire site\'s API integration',
)
);

endif;

}
