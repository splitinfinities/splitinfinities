<?php

require_once __DIR__ . '/vimeo/autoload.php';

function get_vimeo_config() {
	global $wpdb;
	$domain = $_SERVER['SERVER_NAME'];

	$query = 'SELECT `wp_options`.`option_value` FROM `wp_options` ';
	$vimeo_app_id_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_app_id" ';
	$vimeo_app_secret_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_app_secret" ';
	$vimeo_app_token_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_app_token" ';
	$vimeo_page_token_query = $query . 'WHERE `wp_options`.`option_name` = "options_sdo_api_vimeo_page_token" ';

	$vimeo_app_id = $wpdb->get_var( $vimeo_app_id_query );
	$vimeo_app_secret = $wpdb->get_var( $vimeo_app_secret_query );
	$vimeo_app_token = $wpdb->get_var( $vimeo_app_token_query );
	$vimeo_page_token = $wpdb->get_var( $vimeo_page_token_query );

	return array(
		'client_id' => $vimeo_app_id,
		'client_secret' => $vimeo_app_secret,
		'access_token' => $vimeo_app_token,
		'page_access_token' => $vimeo_page_token
	);

}

function get_vimeo_variable() {
	$vimeo_config = get_vimeo_config();
	$lib = new \Vimeo\Vimeo($vimeo_config['client_id'], $vimeo_config['client_secret']);

	if ($vimeo_config['access_token']) {
		$lib->setToken($vimeo_config['access_token']);
	}

	return $lib;
}

function set_longlived_vimeo_token() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

	$token = get_vimeo_variable()->accessToken($_GET['code'], $protocol.$_SERVER['SERVER_NAME'].'/vimeo');

	update_field('sdo_api_vimeo_app_token', $token['body']['access_token'], 'options');

	header('Location: '.$url_to_redirect_to.'/wp-admin/admin.php?page=acf-options-apis'); exit;
}

function get_vimeo_video_params($video_link) {
	$video_id = get_vimeo_id($video_link);
	$vimeo = get_vimeo_variable()->request('/videos/'.$video_id, array('per_page' => 1), 'GET');

	$hq = $vimeo['body']['files'][0];
	$sd = null;
	$hls = null;


	foreach ($vimeo['body']['files'] as $key => $file) {
		if ($file['quality'] === 'hd') {
			if ($hq['fps'] <= $file['fps']) {
				$hq = $file;
			}
		}
		else if ($file['quality'] === 'sd') {
			$sd = $file;
		}
		else if ($file['quality'] === 'hls') {
			$hls = $file;
		}
	}

	$picture = $vimeo['body']['pictures']['sizes'][count($vimeo['body']['pictures']['sizes']) - 1]['link'];

	$videos = array(
		'hq' => $hq,
		'hls' => $hls,
		'sd' => $sd,
		'picture' => $picture
	);

	return $videos;
}
