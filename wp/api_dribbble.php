<?php

function dribbble_request($user, $count = 5) {
	$DRIBBBLE_API_KEY = get_field('sdo_api_dribbble_app_id', 'options');

	$data = json_decode( file_get_contents( 'http://api.dribbble.com/v1/users/' . $user . '/shots?page=1&per_page='.$count.'&access_token=' . $DRIBBBLE_API_KEY ) );

	return $data;
}
