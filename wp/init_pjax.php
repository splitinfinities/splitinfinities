<?php

function pjax_body_class($class = '') {
	echo join( ' ', get_body_class( $class ) );
}

function pjaxify($return = false) {
	if (get_field('enable_ajax', 'options')) {
		if ($return) {
			return 'ajax';
		} else {
			echo ' ajax';
		}
	}
}
