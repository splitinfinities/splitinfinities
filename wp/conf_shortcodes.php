<?php

add_shortcode('smart_image', 'img_smart_image_shortcode');

// [smart_image image_id="ID" bleed="true" zoom="true" retina="true"]a caption with <b>markup</b>[/smart_image]

/**
 * Builds the Smart Image shortcode output.
 *
 * Allows a plugin to replace the content that would otherwise be returned. The
 * filter is 'img_smart_image_shortcode' and passes an empty string, the attr
 * parameter and the content parameter values.
 *
 * The supported attributes for the shortcode are 'id', 'align', 'width', and
 * 'caption'.
 *
 * @since 2.6.0
 *
 * @param array  $attr {
 *     Attributes of the caption shortcode.
 *
 *     @type string $id      ID of the div element for the caption.
 *     @type string $align   Class name that aligns the caption. Default 'alignnone'. Accepts 'alignleft',
 *                           'aligncenter', alignright', 'alignnone'.
 *     @type int    $width   The width of the caption, in pixels.
 *     @type string $caption The caption text.
 *     @type string $class   Additional class name(s) added to the caption container.
 * }
 * @param string $content Shortcode content.
 * @return string HTML content to display the caption.
 */
function img_smart_image_shortcode( $attr, $content = null ) {
	// New-style shortcode with the caption inside the shortcode with the link and image tags.

	$image_meta = wp_get_attachment_metadata( $attr['image_id'] );

	//echo json_encode($image_meta);
	$mobile_detect = new Mobile_Detect;

	if ($mobile_detect->version('IE') === "8.0") {
		$image_large = wp_get_attachment_image_src( $attr['image_id'], 'full');

		$final_image = '<img src="'.make_href_root_relative($image_large[0]).'" class="aligncenter" />';

		return $final_image;
	}

	if (get_field('image_animated_png', $attr['image_id'])) {
		$image_large = wp_get_attachment_image_src( $attr['image_id'], 'full');

		// See if the uploaded image is retina
		if (strpos($image_meta['file'], '@2x') > 0) {
			$attr['retina'] = true;
		}

		if (strpos($image_meta['file'], '@3x') > 0) {
			$attr['super_retina'] = true;
		}


		// If this image has bleed, but it's turned off
		if (!isset($attr['bleed']) || isset($attr['bleed']) && $attr['bleed'] === "false") {
			// set the image width if it's a retina image
			$css_max_width = (isset($attr['retina']) && $attr['retina'] === true) ? 'max-width:'.($image_large[1] / 2).'px;' :'max-width:'.$image_large[1].'px;';

			$css_max_width = (isset($attr['super_retina']) && $attr['super_retina'] === true) ? 'max-width:'.($image_large[1] / 3).'px;' : $css_max_width;
			// set the image width if it's a standard image
			if (isset($attr['retina']) && $attr['retina'] !== true) {
				$css_max_width = (isset($attr['width']) && $attr['width'] !== true) ? 'max-width:'.$attr['width'].'px;' : $css_max_width;
			}
		} else {
			// otherwise, set bleed on.
			$has_bleed = ' has_bleed';
			$css_max_width = 'max-width:100%;';
		}

		$final_image = '<img src="'.make_href_root_relative($image_large[0]).'" style="'.$css_max_width.'" class="aligncenter apng-image" />';

		return $final_image;
	}

	if ($image_meta) {

		// Return the svg instead of the smart iamge stuff here.
		if (strpos($image_meta['file'], '.svg') > 0) {
			$image_large = wp_get_attachment_image_src( $attr['image_id'], 'full');

			$final_image = '<img src="'.$image_large[0].'" class="'.$attr['class'].'" />';
		} else {
			$image_meta = wp_get_attachment_metadata( $attr['image_id'] );

			// gets the images
			$image_large = wp_get_attachment_image_src( $attr['image_id'], 'full');
			$image_md = wp_get_attachment_image_src( $attr['image_id'], 'medium-hero-png');
			$image_sm = wp_get_attachment_image_src( $attr['image_id'], 'small-hero-png');
			$placeholder = wp_get_attachment_image_src( $attr['image_id'], 'apple-square-76'); // update to

			// Sets up the images array
			$images = array(
				'(min-width:1023px), (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi)' => make_href_root_relative($image_large[0]),
				'(min-width:1023px)' => make_href_root_relative($image_large[0]),
				'(min-width:1023px), (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi)' => make_href_root_relative($image_large[0]),
				'(min-width:1023px)' => make_href_root_relative($image_md[0]),
				'(max-width:640px), (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi)' => make_href_root_relative($image_md[0]),
				'(max-width:640px)' => make_href_root_relative($image_sm[0]),
			);

			// encodes the images array for future use in Javascript
			$images = base64_encode(json_encode($images));

			// If this image has zoom enabled, we turn it on here.
			$has_zoom = (isset($attr['zoom']) && $attr['zoom'] === 'true') ? true : false;
			$has_zoom = ($has_zoom) ? ' has_zoom' : '';
			$has_bleed = false;

			$background_color = get_field('image_preload_color', $attr['image_id']);

			$zoom_color = (isset($attr['zoom']) && $attr['zoom'] === 'true') ? ' data-zoom_bg="' . $background_color . '"' : '';

			// if the image hasn't been uploaded through the dashboard, we make it so that a string is echo'd to find those bugs.
			echo ($image_meta === false) ? 'Looks like image ' . $attr['image_id'] . ' hasn\'t been uploaded through the dashboard.':'';

			// Sets the maximum width of the current image.
			$css_max_width = ' style="max-width:100%"';

			// See if the uploaded image is retina
			if (strpos($image_meta['file'], '@2x') > 0) {
				$attr['retina'] = true;
			}

			if (strpos($image_meta['file'], '@3x') > 0) {
				$attr['super_retina'] = true;
			}

			// If this image has bleed, but it's turned off
			if (!isset($attr['bleed']) || isset($attr['bleed']) && $attr['bleed'] === "false") {
				// set the image width if it's a retina image
				$css_max_width = (isset($attr['retina']) && $attr['retina'] === true) ? 'max-width:'.($image_large[1] / 2).'px;' :'max-width:'.$image_large[1].'px;';

				$css_max_width = (isset($attr['super_retina']) && $attr['super_retina'] === true) ? 'max-width:'.($image_large[1] / 3).'px;' : $css_max_width;
				// set the image width if it's a standard image
				if (isset($attr['retina']) && $attr['retina'] !== true) {
					$css_max_width = (isset($attr['width']) && $attr['width'] !== true) ? 'max-width:'.$attr['width'].'px;' : $css_max_width;
				}
			} else {
				// otherwise, set bleed on.
				$has_bleed = ' has_bleed';
				$css_max_width = 'max-width:100%;';
			}

			// Sets the aspect ratio
			$css_padding_bottom = ($image_large[1] !== null && $image_large[2] !== null) ? ( ( $image_large[2] / $image_large[1] ) * 100 ).'%' : 0;

			// begin creating the $final_image video with for the javascript to parse.
			$final_image = '<figure id="' . $attr['image_id'] . '" class="progressive_image js-not_loaded' . $has_zoom . $has_bleed . ' ' . $attr['class'] . '" width="' . $attr['width'] . '" height="' . $attr['height'] . '" style="'. $css_max_width . '" ' . $zoom_color . ' itemscope="" itemtype="http://schema.org/ImageObject">';
			//$final_image .= '<div class="aspect_ratio_placeholder" style="' . $css_max_width . ' background-image:url(\''.make_href_root_relative($placeholder[0]).'\');">';
			$final_image .= '<div class="aspect_ratio_placeholder" style="' . $css_max_width . '">';
			$final_image .= '<div class="aspect_ratio_fill" style="padding-bottom:' . $css_padding_bottom . ';"></div>'; // this div keeps the aspect ratio so the placeholder doesn't collapse
			$final_image .= '<div class="progressive_media is-image_loaded" style="'. $css_max_width . '">';
			$final_image .= '<img src="' . $placeholder[0] . '" class="low_quality_image" style="'. $css_max_width . '" itemscope="contentUrl" content="' . make_href_root_relative($image_lg[0]) . '"/>'; // this is a tiny image with a resolution of e.g. ~27x17 and low quality
			// 		<canvas/> <!-- takes the above image and applies a blur filter -->
			$final_image .= '<div class="js-swap-for-picture" data-image_info="' . $images . '"></div>'; // <!-- the large image to be displayed -->
			$no_script_image = '<img class="progressive_media-noscript js-progressive_media-inner" src="' . make_href_root_relative($image_large[0]) . '" data-action="zoom" />';
			$final_image .= '<noscript>' . $no_script_image . '</noscript>'; // fallback for no JS
			$final_image .= '</div>';
			$final_image .= '</div>';

			// If this image has a caption, echo it here.
			if ($content) {
				$final_image .= '<figcaption class="wp-caption-text">' . $content . '</figcaption>';
			}

			$final_image .= '</figure>';
		}
	} else {
		$image_large = wp_get_attachment_image_src( $attr['image_id'], 'full');

		$final_image = '<img src="'.make_href_root_relative($image_large[0]).'" class="aligncenter" />';
	}

	return $final_image;
}

add_shortcode('gallery', 'gallery_shortcode');

