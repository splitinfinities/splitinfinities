<?php

if (array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'] != 'true') {
	header( 'HTTP_X_PJAX: true');
}

include ('base.html');
?>
