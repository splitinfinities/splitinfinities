<?php

/**
 * Apply theme's stylesheet to the visual editor.
 *
 */
function neighborhood_add_editor_styles() {
	add_editor_style();
}

// attach
add_action( 'init', 'neighborhood_add_editor_styles' );

/**
 * Add dropdown button for extra styles.
 *
 */
function neighborhood_custom_mce_buttons($buttons) {
	//array_unshift($buttons, 'styleselect');
	return $buttons;
}

add_filter('mce_buttons_3', 'neighborhood_custom_mce_buttons');


/*
* Function to add new styles to the MCE editor
*/

function my_mce_before_init_insert_formats( $init_array ) {

	// Define the style_formats array
	$style_formats = array(
		// Each array child is a format with it's own settings
		array(
			'title' => 'Title',
			'block' => 'p',
			'classes' => 'title',
		),
		array(
			'title' => 'h1 (p tag)',
			'block' => 'p',
			'classes' => 'h1',
		),
		array(
			'title' => 'h2 (p tag)',
			'block' => 'p',
			'classes' => 'h2',
		),
		array(
			'title' => 'h3 (p tag)',
			'block' => 'p',
			'classes' => 'h3',
		),
		array(
			'title' => 'h4 (p tag)',
			'block' => 'p',
			'classes' => 'h4',
		),
		array(
			'title' => 'h5 (p tag)',
			'block' => 'p',
			'classes' => 'h5',
		),
		array(
			'title' => 'h6 (p tag)',
			'block' => 'p',
			'classes' => 'h6',
		),
		array(
			'title' => '2 column list',
			'selector' => 'ul',
			'classes' => 'two',
		),
		array(
			'title' => '3 column list',
			'selector' => 'ul',
			'classes' => 'three',
		),
		array(
			'title' => 'Button',
			'block' => 'a',
			'classes' => 'button',
		),
		array(
			'title' => '2 column paragraph',
			'selector' => 'p',
			'classes' => 'two-col-pub',
			'wrapper' => true,
		),
		array(
			'title' => '3 column paragraph',
			'selector' => 'p',
			'classes' => 'three-col-pub',
			'wrapper' => true,
		),
		array(
			'title' => 'Make image zoomable',
			'selector' => 'img',
			'attributes' => array('data-action' => 'zoom'),
		),
		array(
			'title' => 'Raised capital',
			'selector' => 'p',
			'classes' => 'raised-cap',

		)
	);

	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );

	return $init_array;
}
// Attach callback to 'tiny_mce_before_init'
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );

/**
 * Apply theme's stylesheet to the visual editor.
 *
 * @uses add_editor_style() Links a stylesheet to visual editor
 * @uses get_stylesheet_uri() Returns URI of theme stylesheet
 */
function override_neighborhood_tinymce_styles( $mce_init ) {
	// make sure we don't override other custom <code>content_css</code> files
	$editor_style = $mce_init[ 'selector' ];
	if ( isset( $mce_init[ 'body_class' ] ) )
		$editor_style .= ' ' . $mce_init[ 'body_class' ];
	$mce_init[ 'body_class' ] = $editor_style;
	// echo json_encode($mce_init);
	return $mce_init;
}

// attach
add_filter( 'tiny_mce_before_init', 'override_neighborhood_tinymce_styles' );



/**
 * Remove P tag from wordpress img tag in the_content
 *
 */
function filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

add_filter('the_content', 'filter_ptags_on_images');
add_filter('acf_the_content', 'filter_ptags_on_images');
// remove_filter( 'the_content', 'wpautop' );
// remove_filter( 'the_excerpt', 'wpautop' );


/**
 * Add default content to MCE
 *
 */
function neighborhood_editor_content( $content ) {
	$content = '';
	// $content .= '<h3 class="aligncenter">Head</h3>';
	// $content .= '<hr class="aligncenter" />';
	// $content .= '<p class="aligncenter"><strong>Lorem ipsum dolor sit amet</strong>, consectetur adipiscing elit. Aliquam nec tortor tempus, posuere sapien et, eleifend orci. In in quam tellus. Duis viverra fermentum cursus. Suspendisse porttitor ante est, molestie vehicula nunc efficitur vel. Fusce vitae justo non purus pellentesque mollis. Aenean id magna quis risus commodo vestibulum. Nam luctus ac erat sit amet ultricies. Vestibulum vitae tortor sit amet ante elementum semper. Nullam consectetur eros ut mattis tincidunt.</p>';
	return $content;
}

add_filter( 'default_content', 'neighborhood_editor_content' );

/**
 * Remove links on embeded images
 *
 */
function wpb_imagelink_setup() {
    $image_set = get_option( 'image_default_link_type' );

    if ($image_set !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}

add_action('admin_init', 'wpb_imagelink_setup', 10);


/**
 * Swaps images out with preloading images
 */
function neighborhood_swap_all_image_tags($content) {

	//Get all the images and put them into an array, $image_tags
	preg_match_all("/<img[^>]+\>/i", $content, $image_tags, PREG_SET_ORDER);

	$image_ids_in_order = array();

	// Loops though the image tags and prepare the smart image shortcode
	foreach ($image_tags as $image_tag) {
		preg_match_all('/(class)=("[^"]*")/i', $image_tag[0], $this_tags_classes);

		preg_match_all('/(width)=("[^"]*")/i', $image_tag[0], $this_tags_width);
		preg_match_all('/(height)=("[^"]*")/i', $image_tag[0], $this_tags_height);
		preg_match_all('/(data-action)=("[^"]*")/i', $image_tag[0], $this_tags_zoom);

		$this_tags_classes = substr($this_tags_classes[0][0], 7, -1);

		$this_tags_classes = explode(' ', $this_tags_classes);

		foreach($this_tags_classes as $a_class) {

			if (strpos($a_class, 'wp-image-') === 0) {
				preg_match_all('!\d+!', $a_class, $id);
				$image_id = (int) $id[0][0];
			}
		}

		$image_ids_in_order[$image_id] = array('string_to_replace' =>  $image_tag[0], 'width' => (int) substr($this_tags_width[2][0], 1, -1), 'height' => (int) substr($this_tags_height[2][0], 1, -1), 'zoom' => $this_tags_zoom[2][0], 'class' => implode(' ', $this_tags_classes));
	}

	/* BEGIN caption logic */
	$pattern = get_shortcode_regex();
	preg_match_all('/'. $pattern .'/s', $content, $shortcodes, PREG_SET_ORDER);

	$caption_ids = array();
	foreach ($shortcodes as $shortcode) {
		if (strpos($shortcode[0], 'caption') >= 0) {

			preg_match('/(id)=("[^"]*")/i', $shortcode[0], $this_captions_id);
			if ($this_captions_id[0]) {
				preg_match_all('!\d+!', $this_captions_id[0], $id);

				if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $shortcode[0], $matches ) ) {
					$caption_ids[(int) $id[0][0]] = array(
						'caption' => substr(trim( $matches[2] ), 0, -10),
						'string_to_replace' => $shortcode[0]
					);
				}
			}
		}
	}

	foreach($image_ids_in_order as $key => $image_details) {
		$zoom_attr = ($image_details['zoom'] !== null) ? ' zoom="true"' : '';
		$class_attr = ($image_details['class'] !== null) ? ' class="' . $image_details['class'] . '"' : '';
		if (!array_key_exists($key, $caption_ids)) {
			$new_image = do_shortcode('[smart_image image_id="' . $key . '"' . $zoom_attr . ' ' . $class_attr . ' width="'.$image_details["width"].'" height="'.$image_details["height"].'"][/smart_image]');
			$content = str_replace($image_details["string_to_replace"], $new_image, $content);
		} else {
			$new_image = do_shortcode('[smart_image image_id="' . $key . '"' . $zoom_attr . ' width="'.$image_details["width"].'" height="'.$image_details["height"].'"]' . $caption_ids[$key]["caption"] . '[/smart_image]');
			$content = str_replace($caption_ids[$key]["string_to_replace"], $new_image, $content);
		}
	}

	return $content;
}

add_filter('the_content','neighborhood_swap_all_image_tags');
add_filter('acf_the_content','neighborhood_swap_all_image_tags');



// add_action('init', 'add_button');

// function add_button() {
// 	add_filter('mce_external_plugins', 'add_plugin');
// 	add_filter('mce_buttons', 'register_button');
// }
