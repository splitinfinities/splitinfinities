<?php

include_once 'twitter/twitteroauth/twitteroauth.php';

function get_recent_tweet_by_username($username = null) {
	if ($username):
		$twitteruser = $username;
		$number = 1;
		$consumerkey = get_field('sdo_api_twitter_app_consumer_key', 'options');
		$consumersecret = get_field('sdo_api_twitter_app_consumer_secret', 'options');
		$accesstoken = get_field('sdo_api_twitter_app_access_token', 'options');
		$accesstokensecret = get_field('sdo_api_twitter_app_access_token_secret', 'options');

		function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
			$connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
			return $connection;
		}

		$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

		$tweet = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$number."&exclude_replies=true&include_rts=false&contributor_details=true&include_entities=true");

		return $tweet[0];
	endif;
}
