<?php

/**
 * $content_width is a global variable used by WordPress for max image upload sizes
 * and media embeds (in pixels).
 *
 * Example: If the content area is 640px wide, set $content_width = 620; so images and videos will not overflow.
 * Default: 1140px is the default Bootstrap container width.
 */
if (!isset($content_width)) { $content_width = 1300; }

function disable_srcset( $sources ) {
	return false;
}
add_filter( 'wp_calculate_image_srcset', 'disable_srcset' );
/*
 * Initialize all the images for the theme
 */
function our_images_setup() {

	add_image_size( 'apple-square-76', 76 );
	add_image_size( 'apple-square-144', 144 );
	add_image_size( 'apple-square-180', 180 );

	add_image_size( 'android-square-32', 32 );
	add_image_size( 'android-square-192', 192 );

	add_image_size( 'win-square-270', 270 );
	add_image_size( 'win-square-558', 558 );
	add_image_size( 'default-png', 100 );

	// For Small Hero Images
	add_image_size( 'small-hero-png', 640 );

	// For the Medium Hero Images
	add_image_size( 'medium-hero-png', 1024 );

	// For the Large Hero Images
	add_image_size( 'large-hero-png', 1200 );

}
add_action( 'after_setup_theme', 'our_images_setup' );

/**
 * Base64 encode an image from the file path and print it to the
 * @param  string $filename This is the root path to the image
 * @param  string $filetype This is the plain file type for the image
 * @return string           Returns the base64 encoded image to be interpreted by CSS
 */
function base64_encode_image( string $filename, string $filetype) {
	if ($filename) {
		ob_start();
		readfile($filename);
		$content = ob_get_clean();
		return 'data:image/' . $filetype . ';base64,' . base64_encode($content);
	}
}

/*
 * Create a response array for format_responsive_image_array()
 * Example Code:
 * <?php responsive_bg( get_field('acf_field_name') ); ?>
 */
function responsive_bg($img_field) {

	// Set the three images sizes to be used
	$img_information = wp_get_attachment_metadata( $img_field );

	$path = '/wp-content/uploads/' . substr($img_information['file'], 0, 8);
	$file = substr($img_information['file'], 8);

	$large_version = ($img_information['sizes']['large-hero-png']['file'] !== null) ? $path.$img_information['sizes']['large-hero-png']['file'] : $path.$file;
	$medium_version = ($img_information['sizes']['medium-hero-png']['file'] !== null) ? $path.$img_information['sizes']['medium-hero-png']['file'] : $path.$file;
	$small_version = ($img_information['sizes']['small-hero-png']['file'] !== null) ? $path.$img_information['sizes']['small-hero-png']['file'] : $path.$file;

	$relative_path = get_attached_file($img_field);
	$relative_path = str_replace(basename($relative_path), '', $relative_path);

	// Assign the image sizes into an array
	$bg_images = array(
		'default' => $path.$file,
		'mobile' => $small_version,
		'(min-width:320px) and (-webkit-device-pixel-ratio: 2)' => $medium_version,
		'(min-width:320px) and (-webkit-device-pixel-ratio: 3)' => $large_version,
		'(min-width:640px)' => $medium_version,
		'(min-width:640px) and (-webkit-device-pixel-ratio: 2)' => $large_version,
		'(min-width:1000px)' => $large_version,
		'(min-width:1000px) and (-webkit-device-pixel-ratio: 2)' => $path.$file,
		'(min-width:1200px)' => $path.$file
		);

	$the_small_preview = $bg_images['mobile'];

	// Take the $bg_images array and run it through encoding
	// so the array is readable by the browser (defined below)
	$bg_images = format_responsive_image_array($bg_images);

	$background_color = (!empty(get_field('image_preload_color', $img_field))) ? get_field('image_preload_color', $img_field) : get_field('brand_image_preloading_color', 'options');
	$background_color = ( $background_color == '' ) ? '#ECF1F3': $background_color;

	// Echo the cleaned image array, but surround it with
	// 'data-bgimg' so JS can run on it to replace images
	// when the browser is resized
	echo ' data-bgimg="' . $bg_images . '" style="background-color:'.$background_color.';background-image:url(\'' . base64_encode_image($relative_path.basename($small_version), 'jpg') . '\')" ';

}

/*
 * Print an image array for responsive use
 * Used in tandem with responsive_bg()
 */
function format_responsive_image_array(array $images) {
	$json_string = json_encode($images);
	$json_string = base64_encode($json_string);

	return $json_string;
}

/**
 * Return an preformatted smart image
 */
function a_smart_image( $image_sm, $image_md, $image_lg = null ) {

	// Sets up the images array
	$images = array(
		'(max-width:640px)' => $image_sm->url,
		'(max-width:640px) and (-webkit-device-pixel-ratio: 2)' => $image_md->url,
		);

	if ($image_lg) {
		$images['(min-width:640px) and (max-width:1000px)'] = $image_md->url;
		$images['(min-width:640px) and (max-width:1000px) and (-webkit-device-pixel-ratio: 2)'] = $image_lg->url;
		$images['(min-width:1000px)'] = $image_lg->url;
	} else {
		$images['(min-width:640px)'] = $image_md->url;
	}

	// encodes the images array for future use in Javascript
	$images = base64_encode(json_encode($images));

	// Sets the aspect ratio
	$css_padding_bottom = ($image_sm->width !== null && $image_sm->height !== null) ? ( ( $image_sm->height / $image_sm->width ) * 100 ).'%' : 0;

	// begin creating the $final_image video with for the javascript to parse.
	$final_image = '<figure class="progressive_image js-not_loaded bleed" itemscope="" itemtype="http://schema.org/ImageObject">';
	$final_image .= '<div class="aspect_ratio_placeholder" style="background-image:url(\''.$image_sm->url.'\');">';
	$final_image .= '<div class="aspect_ratio_fill" style="padding-bottom:' . $css_padding_bottom . ';"></div>'; // this div keeps the aspect ratio so the placeholder doesn't collapse
	$final_image .= '<div class="progressiveMedia is-imageLoaded">';
	$final_image .= '<img src="' . $image_sm->url . '" class="low-quality-image" itemscope="contentUrl" content="' . $image_lg->url . '"/>'; // this is a tiny image with a resolution of e.g. ~27x17 and low quality
	// 		<canvas/> <!-- takes the above image and applies a blur filter->
	$final_image .= '<div class="js-swap-for-picture" data-image_info="' . $images . '"></div>'; // <!-- the large image to be displayed->
	$no_script_image = '<img class="progressiveMedia-noscript js-progressiveMedia-inner" src="' . $image_md->url . '" data-action="zoom" />';
	$final_image .= '<noscript>' . $no_script_image . '</noscript>'; // fallback for no JS
	$final_image .= '</div>';
	$final_image .= '</div>';

	// If this image has a caption, echo it here.
	if ($content) {
		$final_image .= '<figcaption class="wp-caption-text" itemscope="caption">' . $content . '</figcaption>';
	}

	$final_image .= '</figure>';

	return $final_image;
}

/*
 * Allow SVG's to be uploaded as images
 */
function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

/*
 * Displays SVG's in the media grid
 */
function custom_admin_head() {
	$css = '';

	$css = 'td.media-icon img[src$=".svg"] { width: 100% !important; height: auto !important; }';

	echo '<style type="text/css">'.$css.'</style>';
}
add_action('admin_head', 'custom_admin_head');
