<?php
function neighborhood_acf_flexible_content_layout_title( $title, $field, $layout, $i ) {

	$title = '';

	// load sub field image
	// note you may need to add extra CSS to the page t style these elements
	$title .= '<div class="thumbnail" style="display: inline-block; margin: 0 6px 0 10px;">';

	if ( $image = get_sub_field('background_image') || $color = get_sub_field('background_color') ) {

		$image = wp_get_attachment_image_src( $image, 'default-png');

		$title .= '<img src="' . $image[0] . '" height="36px" style="display: inline-block; margin-top: -28px; position: relative; top: 7px;" />';

	}

	if ( $image = get_sub_field('hero_image') || $color = get_sub_field('background_color')  ) {

		$image = wp_get_attachment_image_src( $image, 'default-png');

		$title .= '<img src="' . $image[0] . '" height="36px" style="display: inline-block; margin-top: -28px; position: relative; top: 7px;" />';

	}

	$title .= '</div>';


	// load text sub field
	if( $text = get_sub_field('section_name') ) {

		$title .= '<h4 style="display: inline-block; margin: 0 6px;">' . $text . ' - <small>' . $layout['label'] . '</small></h4>';

	} else {
		$title .= '<h4 style="display: inline-block; margin: 0 6px;"><small>' . $layout['label'] . '</small></h4>';
	}


	// return
	return $title;

}

// name
add_filter('acf/fields/flexible_content/layout_title', 'neighborhood_acf_flexible_content_layout_title', 10, 4);

