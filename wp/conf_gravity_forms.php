<?php
use Carbon\Carbon as Carbon;

/**
 * Fix Gravity Form Tabindex Conflicts
 * http://gravitywiz.com/fix-gravity-form-tabindex-conflicts/
 */
function gform_tabindexer( $tab_index, $form = false ) {
	$starting_index = 1000; // if you need a higher tabindex, update this number
							//
	if ( $form ) {
		add_filter( 'gform_tabindex_' . $form['id'], 'gform_tabindexer' );
	}
	return GFCommon::$tab_index >= $starting_index ? GFCommon::$tab_index : $starting_index;
}

add_filter( 'gform_tabindex', 'gform_tabindexer', 10, 2 );


/**
 * Style our button correctly
 *
 */
add_filter("gform_submit_button", "form_submit_button", 10, 2);
function form_submit_button($button, $form) {
	return "<button type='submit' id='gform_submit_button_{$form["id"]}' class='gform_button button with-icon' tabindex='1039' onclick='if(window[\"gf_submitting_{$form["id"]}\"]){return false;}window[\"gf_submitting_{$form["id"]}\"]=true;'>Submit&nbsp;<i class='icon icon-arrow-right'></i></button>";
}


/**
 * Processing for Gravity Forms Events page.
 */

add_action( 'gform_after_submission', 'set_post_content', 10, 2 );
function set_post_content( $entry, $form ) {

	// //getting post
	// $post = get_post( $entry['post_id'] );

	// //updating post
	// wp_update_post( $post );

	// $date = $entry[2];
	// $start_time = $date . ' ' . $entry[3];
	// $end_time = $date . ' ' . $entry[4];

	// $start_time = Carbon::createFromFormat('Y-m-d h:i a', $start_time);
	// $end_time = Carbon::createFromFormat('Y-m-d h:i a', $end_time);

	// update_field('event_type', 'event', $entry['post_id']);
	// update_field('event_start_datetime', $start_time, $entry['post_id']);
	// update_field('event_end_datetime', $end_time, $entry['post_id']);
	// update_field('event_location', serialize(Array('address' => $entry[5], 'lat' => $entry[7], 'lng' => $entry[8])), $entry['post_id']);
}
