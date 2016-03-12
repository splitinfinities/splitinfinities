<?php
$raw_hubdia_data = file_get_contents('https://bill_riley.hubdia.com/json');
$usable_hubdia_data = json_decode($raw_hubdia_data);

	// getting your username
	echo $usable_hubdia_data->username;

	// getting your location
	echo $usable_hubdia_data->location_colloquial;

	// accessing your social links
	if (count($usable_hubdia_data->user_platforms) > 0) {
		foreach ($usable_hubdia_data->user_platforms as $platform) {
			echo $platform->permalink;
		}
	}

	// accessing your favorites
	if (count($usable_hubdia_data->favorites) > 0) {
		foreach ($usable_hubdia_data->favorites as $favorite) {
			echo ($favorite->item->name !== null) ? $favorite->item->name : '';
		}
	}

	// accessing the people who inspire you
	foreach ($usable_hubdia_data->inspirations as $inspiration) {
		echo $inspiration->following->first_name;
	}

	// accessing the people who are inspired by you
	foreach ($usable_hubdia_data->inspirators as $inspirator) {
		echo $inspirator->follower->first_name;
	}
?>
