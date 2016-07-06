<?php

// TODO:
// Make it so that if we need to use this, you need to call a special
// function in the includes.
// Call that function automatically in the admin dashboard.

function instagram_request($user = "312786232", $count = 5) {
	$INSTAGRAM_API_KEY = get_field('sdo_api_instagram_app_id', 'options');

	$data = json_decode( file_get_contents( 'https://api.instagram.com/v1/users/' . $user . '/media/recent/?client_id=' . $INSTAGRAM_API_KEY . '&count='.$count ) );

	return $data;
}

function get_instagram_posts() {

	foreach(get_field('sdo_api_instagram_usernames', 'options') as $user) {
		$instagram = array(
			array(
				'instagram_id' => $user['user_id'],
				'username' => $user['username']
			)
		);
	}

	$all_posts = array();

	if (get_transient( 'instagram' ) !== false) {
		$all_posts = get_transient( 'instagram' );
	}
	else {
		while ( !empty( $instagram ) ) {
			$username = array_shift( $instagram );
			$pics = instagram_request( $username['instagram_id'] )->data;

			while ( !empty( $pics ) ) {
				$pic = array_shift( $pics );

				$source = ($pic->type === "video") ? $pic->videos->standard_resolution->url : $pic->images->standard_resolution->url;

				$posts = array( intval( $pic->created_time ) => array(
					"platform" => "instagram",
					"media" => $pic->type,
					"name" => $pic->user->full_name,
					"profile_link" => "https://instagram.com/".$pic->user->username,
					"avatar" => $pic->user->profile_picture,
					"likes" => $pic->likes->count,
					"source" => $source,
					"poster" => $pic->images->standard_resolution->url,
					"link" => $pic->link,
					"date" => intval( $pic->created_time ),
					"comment" => $pic->caption->text
					)
				);

				$all_posts = array_merge($posts, $all_posts);
			}
		}

		set_transient( 'instagram', $all_posts, 8 * HOUR_IN_SECONDS );
	}

	return $all_posts;
}
