<?php
use Carbon\Carbon as Carbon;

/**
 * Check to see if the element is empty
 * @param  string  $element HTML representation of an element
 * @return boolean          returns true if element is true
 */
function is_element_empty($element) {
	$element = trim($element);
	return !empty($element);
}

/**
 * Get a search form if the ajax is activated
 * @return string HTML representation of the search form
 */
function neighborhood_get_search_form() {
	if (pjaxify(true)):
		partial('searchform', 'ajax');
	else:
		partial('searchform');
	endif;
}
add_filter('get_search_form', 'neighborhood_get_search_form');

/**
 * Add page slug to body_class() classes if it doesn't exist
 * @param  Array $classes array of already added classes
 * @return Array          Array of classes to be added to the DOM
 */
function neighborhood_body_class($classes) {
  // Add post/page slug
	if (is_single() || is_page() && !is_front_page()) {
		if (!in_array(basename(get_permalink()), $classes)) {
			$classes[] = basename(get_permalink());
		}
	}
	return $classes;
}
add_filter('body_class', 'neighborhood_body_class');

/**
 * Add body class if sidebar is active
 * @param  Array $classes Add class if the sidebar is active
 * @return Array          Classes array to be added to the DOM
 */
function neighborhood_sidebar_body_class($classes) {
	if (display_sidebar()) {
		$classes[] = 'sidebar-primary';
	}
	return $classes;
}
add_filter('body_class', 'neighborhood_sidebar_body_class');


/**
 * Print a single event - consumed on the schedule page.
 * @param  Array $event   Event information to be processed.
 */
function print_the_event($event) {
	if ($event['whos_involved']) {
		foreach ( $event['whos_involved'] as $key => $val ) {
			if ( is_int( $val['speaker'] ) ) {
				$event['whos_involved'][$key] = array(
					'name' => get_the_title($val['speaker']),
					'job_title' => get_field('company', $val['speaker']),
					'photo' => make_href_root_relative( get_field( 'photo', $val['speaker'] ) ) );
			}
		}
	}

	if ($event['start_time']) {
		$start_time = new Carbon; $start_time = $start_time->createFromTimestampUTC($event['start_time']);
	}

	if ( $event['end_time'] !== '' ) {
		$end_time = new Carbon; $end_time = $end_time->createFromTimestampUTC($event['end_time']);
	}

	if ($event['location']) {
		$extended_address = explode(', ', $event['location']['address']);

		$event['location']['address_array'] = $extended_address;
		$event['location']['link_url'] = htmlspecialchars('https://www.google.com/maps/dir/Current+Location/'.$event['location']['address']);
	}

	?>
	<li class="caf <?php echo $event['event_type']; ?>" data-json='<?php echo base64_encode( json_encode( $event ) ); ?>' data-func="schedule_expand_details">
		<div class="time">
			<p><?php echo ($event['start_time']) ? str_replace(array('am','pm'),array(' a.m',' p.m'),$start_time->format('g:ia')) : "TBD" ; ?><?php if ( $event['end_time'] !== '' ) { ?>&nbsp;- <?php echo str_replace('12:00 a.m.', 'Midnight', str_replace(array('am','pm'),array(' a.m.',' p.m.'),$end_time->format('g:ia'))); ?><?php } ?></p>
		</div>
		<div class="details">
			<h2><?php echo $event['event_title']; ?></h2>
			<p><?php echo $event['event_excerpt']; ?></p>
		</div>
		<paper-ripple class="circle" fit></paper-ripple>
	</li>
	<?php
}

/**
 * Retrieve any number of adjacent posts with the same post type.
 *
 * Can either be next or previous posts.
 *
 * @see get_adjacent_posts()
 * @param int $num The number of adjacent posts to return.
 * @param bool $previous Optional. Whether to retrieve previous posts.
 * @return mixed Post array if successful. Null if global $post is not set.
 * Empty string if no corresponding post exists.
 */
function multiple_get_adjacent_posts($num = 1, $previous = true) {
	global $post, $wpdb;
	if ( empty( $post ) )
		return null;
	$current_post_date = $post->post_date;
	$adjacent = $previous ? 'previous' : 'next';
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';
	$where = $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish'", $current_post_date, $post->post_type);
	$sort  = "ORDER BY p.post_date $order LIMIT $num";
	$results = $wpdb->get_results("SELECT p.* FROM $wpdb->posts AS p $where $sort");

	if ( null === $results )
		$results = '';

	if ($previous) {
		if (count($results) !== 4) {
			$num = ( ( count( $results ) - 4 ) * -1 );
			$where = $wpdb->prepare("WHERE p.post_type = %s AND p.post_status = 'publish'", $post->post_type);
			$sort  = "ORDER BY p.post_date DESC LIMIT $num";
			$other_results = $wpdb->get_results("SELECT p.* FROM $wpdb->posts AS p $where $sort");
			$results = array_merge($results, $other_results);
		}
	}

	return $results;
}
/**
 * Returns links for custom adjacent posts.
 *
 * @param int The number of posts to return.
 * @param bool Whether to return previous posts.
 * @return string HTML hyperlinks if posts are available, empty string if not.
 */
function multiple_adjacent_post_links($num = 2, $previous = true) {
	$html = '';

	if ( $adjacentPosts = multiple_get_adjacent_posts($num, $previous) ) {
		foreach ($adjacentPosts as $adjacentPost) {
			$html .= '<a href="'.get_permalink($adjacentPost) .'">'.$adjacentPost->post_title .'</a>';
		}
	}

	return $html;
}
/**
 * Returns links for next posts.
 *
 * @param int The number of posts to return.
 * @return string HTML hyperlinks or empty string.
 */
function multiple_next_post_links($num = 2) {
	return multiple_adjacent_post_links($num, false);
}
/**
 * Returns links for previous posts.
 *
 * @param int The number of posts to return.
 * @return string HTML hyperlinks or empty string.
 */
function multiple_previous_post_links($num = 2) {
	return multiple_adjacent_post_links($num);
}

/**
 * Returns links for previous posts.
 *
 * @param	int	$color	The hex code for a color
 * @return	boolean		The
 */
function is_reverse_contrast($hex) {
	if ($hex) {
		$hex       = str_replace('#', '', $hex);
		$r         = (hexdec(substr($hex, 0, 2)) / 255);
		$g         = (hexdec(substr($hex, 2, 2)) / 255);
		$b         = (hexdec(substr($hex, 4, 2)) / 255);
		$lightness = round((((max($r, $g, $b) + min($r, $g, $b)) / 2) * 100));
		$return_value = ( $lightness <= 150 ) ? ' light' : '';
		$return_value = ( $lightness <= 65 ) ? ' dark' : $return_value;
	} else {
		$return_value = '';
	}

	return $return_value;
}

/**
 * Outputs SVG files without executing their interior code.
 *
 * @param	int		$file_id	the ID of the uploaded image
 * @return	string	The raw SVG code
 */
function include_svg($file_id) {
	if ($file_id) {
		$relative_path = get_attached_file($file_id);
		ob_start();
		readfile($relative_path);
		$content = ob_get_clean();

		return $content;
	}
}


function include_video($vimeo_src, $fallback_image, $action = array( 'loop', 'muted', 'autoplay') ) {
	if (get_transient($vimeo_src)) {
		$vimeo = get_transient($vimeo_src);
	} else {
		$vimeo = get_vimeo_video_params($vimeo_src);
		set_transient($vimeo_src, $vimeo, 30 * DAY_IN_SECONDS);
	}

	$playing_video = (in_array('autoplay', $action)) ? 'playing_video' : '';
	$show_controls = (in_array('controls', $action)) ? 'controls' : '';

	$deviceimage = ($vimeo['picture']) ? $vimeo['picture'] : wp_get_attachment_image_src( $fallback_image, 'full')[0];

	$video .= sprintf( '<div class="video-indicator %s"><video preload="auto" width="%s" height="%s" %s poster="%s" class="caf" data-func="play_video">', $playing_video, $vimeo['width'], $vimeo['height'], implode(' ', $action), $deviceimage);
	$video .= sprintf( '<source src="%s" type="application/x-mpegURL" />', $vimeo['hls']['link_secure'] );
	$video .= sprintf( '<source src="%s" type="video/mp4" media="(min-device-pixel-ratio:2), (-webkit-min-device-pixel-ratio:2), (min--moz-device-pixel-ratio:2), (-o-min-device-pixel-ratio:2)" />', $vimeo['hq']['link_secure'] );
	$video .= sprintf( '<source src="%s" type="video/mp4" media="(max-device-pixel-ratio:1), (-webkit-max-device-pixel-ratio:1), (max--moz-device-pixel-ratio:1), (-o-max-device-pixel-ratio:1)" />', $vimeo['sd']['link_secure'] );
	$video .= do_shortcode('[smart_image image_id="' . get_sub_field('media_id') . '" zoom="true"]');
	$video .= sprintf( '</video></div>' );

	return $video;
}
