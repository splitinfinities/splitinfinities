<?php /* global $post; */ ?>
<?php
if (!include 'custom/' . $post->post_name . '-' . sanitize_title( get_sub_field( 'section_name' ) ) . '.php') {
	include 'custom/' . $post->post_name . '-' . sanitize_title( get_sub_field( 'section_name' ) ) . '.php';
} ?>
