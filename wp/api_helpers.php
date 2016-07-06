<?php

/**
 * FACEBOOK: This function pulls in a Facebook pages feed.
 * @return GraphEdge or GraphNode Object, returns an object with the requested content.
 */
function get_page_feed() {
	global $facebook_page_id;
	$config = get_facebook_config();

	$fbApp = new Facebook\FacebookApp($config['app_id'], $config['app_secret']);
	$request = new Facebook\FacebookRequest($fbApp, $config['access_token'], 'GET', '/'.$facebook_page_id.'?fields=posts{full_picture,message}');

	try {
		$fbClient = new Facebook\FacebookClient();

		$response = $fbClient->sendRequest($request);

		return $response->getGraphNode();

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

/**
 * FACEBOOK: This function pulls in a Facebook page's reviews.
 * @return GraphEdge or GraphNode Object, returns an object with the requested content.
 */
function get_facebook_testimonial($testimonial_url) {
	global $facebook_page_id;

	if ($testimonial_url !== '') {

		$review_story_id = explode('/', $testimonial_url);

		$review_story_id = $review_story_id[5];

		$config = get_facebook_config();

		$fbApp = new Facebook\FacebookApp($config['app_id'], $config['app_secret']);
		$request = new Facebook\FacebookRequest($fbApp, $config['page_access_token'], 'GET', '/'.$facebook_page_id.'/ratings?fields=open_graph_story{id,start_time},review_text,reviewer');

		try {
			$fbClient = new Facebook\FacebookClient();

			$response = $fbClient->sendRequest($request);

			foreach ($response->getGraphEdge() as $graph_item) {

				if ($graph_item['open_graph_story']['id'] === $review_story_id) {
					return $graph_item->asArray();
				}
			};

			return 'The function returned an error: We couldn&rsquo;t find a testimonial that matches that link!';

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			throw new ErrorException('NEIGHBORHOOD_FB_1: ' . $e->getMessage(). ' Try logging in with Facebook to fix this issue. ', '1', 0);
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			throw new ErrorException('NEIGHBORHOOD_FB_2: ' . $e->getMessage(), '2', 0);
		}
	}
}

/**
 * ALL: This function returns the most recent post from a source based on a user ID.
 * @return Array Items that will provide information to visit the content.
 */
function grab_users_preferred_content($type, $this_user) {

	$social_platforms = get_field('social_platforms', 'user_'.$this_user['ID']);

	$object_to_return = (object) array(
		'caption' => null,
		'type' => null,
		'image_md' => null,
		'image_lg' => null,
		'image_sm' => null,
		'video_url' => null,
		'link' => null,
		'title' => null
	);

	switch ($type):
		/* Favorite Project */
		case 'favorite_project':
			$post = get_sub_field('favorite_project');

			$large = get_field('preview_image', $post);
			$medium = get_field('preview_image', $post);
			$small = get_field('preview_image', $post);

			$permalink = get_permalink($post);
			$post_title = get_the_title($post);

			$object_to_return = (object) array(
				'caption' => 'Favorite Project',
				'type' => 'image',
				'image_lg' => $large,
				'image_md' => $medium,
				'image_sm' => $small,
				'video_url' => null,
				'link' => $permalink,
				'title' => $post_title
			);

		break;
		/* Latest Post */
		case 'post':

			$args = array( 'post_type' => 'blog', 'posts_per_page' => '1', 'nopaging' => true,  'author' => $this_user['ID'], 'order' => 'DESC' );

			$recent_post = new WP_Query($args);

			while ( $recent_post->have_posts() ): $recent_post->the_post();

				$object_to_return = (object) array(
					'caption' => 'Latest post',
					'type' => 'image',
					'image_sm' => get_field( 'preview_image', get_the_ID() ),
					'image_md' => get_field( 'preview_image', get_the_ID() ),
					'image_lg' => get_field( 'preview_image', get_the_ID() ),
					'video_url' => null,
					'link' => get_permalink( get_the_ID() ),
					'title' => get_the_title()
				);

			endwhile;

			wp_reset_postdata();

		break;
		/* Instagram */
		case 'instagram':
			foreach($social_platforms as $social_platform):
				if ($social_platform['social_platforms'] === $type):
					/* Do Instagram Logic */
					$instagram = instagram_request($social_platform['user_id'], 1);

					if ($instagram->data[0]->type === "image"):
						$object_to_return = (object) array(
							'caption' => 'Latest Instagram',
							'type' => $instagram->data[0]->type,
							'image_lg' => null,
							'image_md' => $instagram->data[0]->images->standard_resolution,
							'image_sm' => $instagram->data[0]->images->low_resolution,
							'video_url' => null,
							'link' => $instagram->data[0]->link,
							'title' => $instagram->data[0]->caption->text
						);

					elseif ($instagram->data[0]->type === "video"):
						$object_to_return = (object) array(
							'caption' => 'Latest Instagram',
							'type' => $instagram->data[0]->type,
							'image_lg' => null,
							'image_md' => $instagram->data[0]->images->standard_resolution,
							'image_sm' => $instagram->data[0]->images->low_resolution,
							'video_mp4_url' => $instagram->data[0]->videos->standard_resolution->url,
							'video_webm_url' => str_replace('.mp4', '.webm', $instagram->data[0]->videos->standard_resolution->url),
							'link' => $instagram->data[0]->link,
							'title' => $instagram->data[0]->caption->text
						);
					endif;
				endif;
			endforeach;
		break;
		/* Facebook */
		case 'facebook':
			foreach($social_platforms as $social_platform):
				if ($social_platform['social_platforms'] === $type):
					/* Do Facebook Logic */
				endif;
			endforeach;
		break;
		/* Dribbble */
		case 'dribbble':
			foreach($social_platforms as $social_platform):
				if ($social_platform['social_platforms'] === $type):

					/* Do Dribbble Logic */
					$dribbble = dribbble_request($social_platform['username'], 1);

					$small = (object) array(
						'url' => $dribbble[0]->images->teaser,
						'width' => $dribbble[0]->width,
						'height' => $dribbble[0]->height,
					);

					$medium = (object) array(
						'url' => $dribbble[0]->images->normal,
						'width' => $dribbble[0]->width,
						'height' => $dribbble[0]->height,
					);

					$large = (object) array(
						'url' => $dribbble[0]->images->hidpi,
						'width' => $dribbble[0]->width,
						'height' => $dribbble[0]->height,
					);

					$object_to_return = (object) array(
						'caption' => 'Latest Dribbble',
						'type' => 'image',
						'image_lg' => $large,
						'image_md' => $medium,
						'image_sm' => $small,
						'video_url' => null,
						'link' => $dribbble[0]->html_url,
						'title' => $dribbble[0]->title
					);

				endif;
			endforeach;
		break;
		/* Twitter */
		case 'tweet':
			foreach($social_platforms as $social_platform):
				if ($social_platform['social_platforms'] === 'twitter'):
					/* Do Twitter Logic */
					$tweet = get_recent_tweet_by_username($social_platform['username']);

					$object_to_return = (object) array(
						'caption' => 'Latest Tweet',
						'type' => 'copy',
						'copy' => $tweet->text,
						'background_color' => $tweet->user->profile_background_color,
						'background_image' => $tweet->user->profile_background_image_url_https,
						'link' => 'https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id,
						'title' => $tweet->user->screen_name.' on Twitter'
					);
				endif;
			endforeach;
		break;
		/* Codepen */
		case 'codepen':
			foreach($social_platforms as $social_platform):
				if ($social_platform['social_platforms'] === $type):
					/* Do Codepen Logic */
					$latest_pen = get_codepen_feed('http://codepen.io/' . $social_platform['username'] . '/public/feed/');

					$object_to_return = (object) array(
						'caption' => 'Latest Codepen',
						'type' => 'interactive',
						'image_lg' => null,
						'image_md' => null,
						'image_sm' => null,
						'video_url' => null,
						'code' => '[codepen_embed height="700" theme_id="20900" slug_hash="'.$latest_pen->pen_id.'" default_tab="result" user="'.$latest_pen->user.'"][/codepen_embed]',
						'link' => $latest_pen->permalink,
						'title' => str_replace('"', '\'', $latest_pen->title)
					);

				endif;
			endforeach;
		break;
		/* Medium */
		case 'medium':
			foreach($social_platforms as $social_platform):
				if ($social_platform['social_platforms'] === $type):
					/* Do Medium Logic */
					$latest_post = get_medium_feed('https://medium.com/feed/@' . $social_platform['username'] . '/');

					$object_to_return = (object) array(
						'caption' => 'Latest Medium',
						'type' => 'image',
						'image_lg' => (object) $latest_post['image_lg'],
						'image_md' => (object) $latest_post['image_md'],
						'image_sm' => (object) $latest_post['image_sm'],
						'video_url' => null,
						'link' => $latest_post['permalink'],
						'title' => str_replace('"', '\'', $latest_post['title'])
					);


				endif;
			endforeach;
		break;
		default:

		break;
	endswitch;

	return (object) $object_to_return;
}
