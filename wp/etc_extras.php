<?php
/**
 * Clean up the_excerpt()
 */
function neighborhood_excerpt_more() {
	global $post;
	if ($post->post_type == 'something') {
		return '';
	}

	return ' &hellip; <p><a class="more-link button pull-right ' . pjaxify(true) . '" href="' . get_permalink() . '">Read&nbsp;more&nbsp;</a></p>';
}

add_filter('excerpt_more', 'neighborhood_excerpt_more');

/**
 * Clean up the_content()
 */
function modify_read_more_link() {
	return '<a class="more-link button grey ' . pjaxify(true) . '" href="' . get_permalink() . '">Read&nbsp;more&nbsp;</a>';
}

add_filter( 'the_content_more_link', 'modify_read_more_link' );

/**
 * word wrapper - eliminate widows with non-breaking spaces (&nbsp;)
 */
function word_wrapper( $text, $minWords = 3, $nonbreaking_space_count = 1 ) {
	$nbsp_str = '';

	for ($i = 0; $i < $nonbreaking_space_count; $i++) {
		$nbsp_str .= '&nbsp;';
	}

	$return = $text;
	$arr = explode( ' ', $text );
	if ( count( $arr ) >= $minWords ) {
		$arr[count( $arr ) - 2] .= $nbsp_str . $arr[count($arr) - 1];
		array_pop( $arr );
		$return = implode( ' ', $arr );
	}
	return $return;
}

/**
 * partial - Load partials without dealing with writing the directory
 */
function partial( $slug, $name = null ) {
	get_template_part('partials/' . $slug, $name);
}

/**
 * get_youtube_id - get youtube id from any kind of url (http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id)
 */
function get_youtube_id($url) {
	if (preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches)) {
		return $matches[1];
	} else {
		return false;
	}
}

/**
 * get_vimeo_id - get vimeo id from any kind of url (http://stackoverflow.com/questions/10488943/easy-way-to-get-vimeo-id-from-a-vimeo-url)
 */
function get_vimeo_id($url) {
	if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $url, $matches)) {
		return $matches[5];
	} else {
		return false;
	}
}

// add hook
add_filter( 'wp_nav_menu_objects', 'neighborhood_wp_nav_menu_objects_sub_menu', 10, 2 );

// filter_hook function to react on sub_menu flag
function neighborhood_wp_nav_menu_objects_sub_menu( $sorted_menu_items, $args ) {
	if ( isset( $args->sub_menu ) ) {
		$root_id = 0;

	// find the current menu item
		foreach ( $sorted_menu_items as $menu_item ) {
			if ( $menu_item->current ) {
		// set the root id based on whether the current menu item has a parent or not
				$root_id = ( $menu_item->menu_item_parent ) ? $menu_item->menu_item_parent : $menu_item->ID;
				break;
			}
		}

	// find the top level parent
		if ( ! isset( $args->direct_parent ) ) {
			$prev_root_id = $root_id;
			while ( $prev_root_id != 0 ) {
				foreach ( $sorted_menu_items as $menu_item ) {
					if ( $menu_item->ID == $prev_root_id ) {
						$prev_root_id = $menu_item->menu_item_parent;
			// don't set the root_id to 0 if we've reached the top of the menu
						if ( $prev_root_id != 0 ) $root_id = $menu_item->menu_item_parent;
						break;
					}
				}
			}
		}

		$menu_item_parents = array();
		foreach ( $sorted_menu_items as $key => $item ) {
	  // init menu_item_parents
			if ( $item->ID == $root_id ) $menu_item_parents[] = $item->ID;

			if ( in_array( $item->menu_item_parent, $menu_item_parents ) ) {
		// part of sub-tree: keep!
				$menu_item_parents[] = $item->ID;
			} else if ( ! ( isset( $args->show_parent ) && in_array( $item->ID, $menu_item_parents ) ) ) {
		// not part of sub-tree: away with it!
				unset( $sorted_menu_items[$key] );
			}
		}

		return $sorted_menu_items;
	} else {
		return $sorted_menu_items;
	}
}

// Intelligent breadcrumbs
function breadcrumbs($optional_last_title = null) {
	global $post;
	global $wp_query;
	echo '<ul class="breadcrumbs">';
	if (!is_home()) {
		echo '<li><a href="' . get_option('home'). '">Home</a></li>';
		echo '<li class="separator"> '.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).' </li>';
		if (is_category() || is_single()) {
			echo '<li>';
			echo '<a href="'.get_post_type_archive_link($post->post_type).'" title="'.$post->post_type.'">'.$post->post_type.'</a>';
			the_category(' </li><li class="separator"> '.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).' </li>');
			if (is_single()) {
				echo '<li class="separator"> '.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).' </li>';
				$tax_title = wp_get_object_terms( $post->ID, 'news_category', array( 'fields' => 'all' ) );
				$term = array_shift( $tax_title );
				echo '<li><a href="' . get_term_link( $term->slug, 'news_category' ) . '" title="' . $term->name . '">' . $term->name . '</a></li>';
				echo '<li class="separator"> '.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).' </li>';
				echo '<li><strong>'.get_the_title().'</strong></li>';
			}
		} elseif (is_page()) {
			if($post->post_parent){
				$anc = get_post_ancestors( $post->ID );
				$title = get_the_title();
				foreach ( $anc as $ancestor ) {
					$output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">'.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).'</li>';
				}
				echo $output;
				echo ($optional_last_title) ? '<li><strong title="' . $optional_last_title . '"> ' . $optional_last_title . '</strong></li>' : '<li><strong title="'.$title.'"> '.$title.'</strong></li>';
			} else {
				echo '<li><strong title="'.get_the_title().'"> '.get_the_title().'</strong></li>';
			}
		} elseif (is_archive()) {
			if (is_tax()) {
				echo '<li><a href="' . get_post_type_archive_link($post->post_type).'" title="'.$post->post_type.'">'.$post->post_type.'</a></li>';
				echo '</li><li class="separator"> '.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).' </li><li>';
				echo '<strong title="' . $wp_query->queried_object->name . '">' . $wp_query->queried_object->name . '</strong></li>';
			} else {
				echo '<li><strong title="'.$wp_query->queried_object->name.'"> '.$wp_query->queried_object->name.'</strong></li>';
			}
		}
		elseif (is_search()) {
			echo '<li><a href="'.get_post_type_archive_link('news').'" title="News">News</a></li>';
			echo '<li class="separator"> '.print_image('arrow_breadcrumbs.svg', array('width' => '5', 'height' => '9')).' </li>';
			echo"<li><strong>Search: " . get_search_query() . '</strong></li>';
		}

	}
	elseif (is_tag()) {single_tag_title();}
	elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
	elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
	elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
	elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
	elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
	elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
	echo '</ul>';
}

/**
 * Make a full url relative
 */
function make_href_root_relative($input) {
	return preg_replace('!http(s)?://' . $_SERVER['SERVER_NAME'] . '/!', '/', $input);
}

/**
 * Clean up the title attributes on elements
 */
function remove_title_attributes($input) {
	return preg_replace('/\s*title\s*=\s*(["\']).*?\1/', '', $input);
}

/**
 * Better print images.
 */
function print_image( $src = null, $attributes = array() ) {
	$image = '<img src="' . get_template_directory_uri() . '/assets/img/' . $src . '" ';

	if ( !array_key_exists( 'alt', $attributes ) ) {
		$attributes['alt'] = " > ";
	}

	foreach($attributes as $key => $val) {
		$image .= $key . '="' . $val . '"';
	}

	$image .= '>';

	return $image;
}

/**
 * Better, cleaner walker for the Navigation from the menus in wordpress
 */
class Clean_Page_Walker extends Walker_Page {
	function start_lvl(&$output, $depth = 0, $args = array()) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul>\n";
	}
	function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';
		extract($args, EXTR_SKIP);
		$class_attr = 'page-'.$page->post_name;
		if ( !empty($current_page) ) {
			$_current_page = get_page( $current_page );
			if ( (isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors)) || ( $page->ID == $current_page ) || ( $_current_page && $page->ID == $_current_page->post_parent ) ) {
				$class_attr .= ' active';
			}
		} elseif ( (is_single() || is_archive()) && ($page->ID == get_option('page_for_posts')) ) {
			$class_attr .= ' active';
		}
		if ( $class_attr != '' ) {
			$class_attr = ' class="' . $class_attr . '"';
			$link_after = $link_after;
		}
		$output .= $indent . '<li' . $class_attr . '><a href="' . make_href_root_relative(get_page_link($page->ID)) . '"' . $class_attr . '>' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;
			$output .= " " . mysql2date($date_format, $time);
		}
	}
}

/**
 * List pages using the cleaner walker.
 */
function list_pages($args) {

	$walker = new Clean_Page_Walker();

	$needed_args = array(
		'title_li' => '',
		'walker' => $walker,
		);

	$final_args = array_merge($args, $needed_args);

	return wp_list_pages( $final_args );
}


/**
 * Check if the passed page is a child or top level parent
 */
function is_tree($pid) {
	global $post;
	if(is_page()&&($post->post_parent==$pid||is_page($pid)))
		return true;
	else
		return false;
};


/**
 * Print numeric pagination
 */
function numeric_posts_nav($custom_query = null) {

	if( is_singular() && $custom_query === null )
		return;

	global $wp_query;

	if ($custom_query)
		$wp_query = ($custom_query !== null) ? $custom_query : $wp_query;

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/**	Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/**	Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="numbered-pagination"><ul class="unstyled inline">' . "\n";

	/**	Previous Post Link */
	if ( get_previous_posts_link() )
		printf( '<li><a href="%s" class="prev">%s</a></li>' . "\n", get_previous_posts_page_link(), print_image('arrow_blue.svg') );

	/**	Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			echo '<li>…</li>';
	}

	/**	Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/**	Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			echo '<li>…</li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/**	Next Post Link */
	if ( get_next_posts_link() )
		printf( '<li><a href="%s" class="next">%s</a></li>' . "\n", get_next_posts_page_link(), print_image('arrow_blue.svg') );

	echo '</ul></div>' . "\n";

}


/**
 * List all the taxonomies
 */
function list_taxonomies($post_type = "post", $include_numbers = false) {
	$args = array( 'hide_empty' => false );
	$terms = get_terms( $post_type, $args );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$count = count( $terms );
		$term_list = '';
		$total_count = 0;

		foreach ( $terms as $term ) {
			if ($term->term_id != 1) {
				if (is_tax( $post_type, $term->name )) {
					$term_list .= '<li class="column active">';
				} else {
					$term_list .= '<li class="column">';
				}

				$term_list .= '<a href="' . get_term_link( $term ) . '" title="' . sprintf( __( 'View all post filed under %s', 'neighborhood' ), $term->name ) . '">' . $term->name;
				if ($include_numbers) {
					$term_list .= '<span class="num">' . $term->count . '</span>';
				}
				$term_list .= '</a></li>';
			}
		}

		echo $term_list;
	}
}


/**
 * convert a json tweet to html
 */
function json_tweet_text_to_HTML($tweet, $links=true, $users=true, $hashtags=true) {
	$return = $tweet->text;

	$entities = array();

	if($links && is_array($tweet->entities->urls))
	{
		foreach($tweet->entities->urls as $e)
		{
			$temp["start"] = $e->indices[0];
			$temp["end"] = $e->indices[1];
			$temp["replacement"] = "<a href='".$e->expanded_url."' target='_blank'>".$e->display_url."</a>";
			$entities[] = $temp;
		}
	}
	if($users && is_array($tweet->entities->user_mentions))
	{
		foreach($tweet->entities->user_mentions as $e)
		{
			$temp["start"] = $e->indices[0];
			$temp["end"] = $e->indices[1];
			$temp["replacement"] = "<a href='https://twitter.com/".$e->screen_name."' target='_blank'>@".$e->screen_name."</a>";
			$entities[] = $temp;
		}
	}
	if($hashtags && is_array($tweet->entities->hashtags))
	{
		foreach($tweet->entities->hashtags as $e)
		{
			$temp["start"] = $e->indices[0];
			$temp["end"] = $e->indices[1];
			$temp["replacement"] = "<a href='https://twitter.com/hashtag/".$e->text."?src=hash' target='_blank'>#".$e->text."</a>";
			$entities[] = $temp;
		}
	}

	usort($entities, function($a,$b){return($b["start"]-$a["start"]);});


	foreach($entities as $item)
	{
		$return = substr_replace($return, $item["replacement"], $item["start"], $item["end"] - $item["start"]);
	}

	return($return);
}

/**
 * pull a page's id by it's slug
 */
function get_id_by_slug($page_slug) {
	$page = get_page_by_path($page_slug);
	if ($page) {
		return $page->ID;
	} else {
		return null;
	}
}

/**
 * Add the caldera forms assets on a page if it requires it.
 */
function add_caldera_forms_assets_ajax() {
	sendo()->capture_javascript_start(); ?>
		<script type="text/javascript">window.a = $;</script>

	<?php
	sendo()->capture_javascript_end();

	sendo()->prepend_css(get_site_url() . '/wp-content/plugins/caldera-forms/assets/css/caldera-grid.css?ver=' . CFCORE_VER)
		   ->prepend_css(get_site_url() . '/wp-content/plugins/caldera-forms/assets/css/fields.min.css?ver=' . CFCORE_VER)
		   ->append_javascript(get_site_url() . '/wp-content/plugins/caldera-forms/assets/js/jquery.baldrick.min.js?ver=' . CFCORE_VER)
		   ->append_javascript(get_site_url() . '/wp-content/plugins/caldera-forms/assets/js/ajax-core.min.js?ver=' . CFCORE_VER)
		   ->append_javascript(get_site_url() . '/wp-content/plugins/caldera-forms/assets/js/fields.min.js?ver=' . CFCORE_VER)
		   ->append_javascript(get_site_url() . '/wp-content/plugins/caldera-forms/assets/js/parsley.min.js?ver=' . CFCORE_VER)
		   ->append_javascript(get_site_url() . '/wp-content/plugins/caldera-forms/assets/js/frontend-script-init.min.js?ver=' . CFCORE_VER);
}

/**
 * convert a word to a number
 */
function convert_to_number($digit)
{
    switch ($digit)
    {
        case "one":
            return 1;
        case "two":
            return 2;
        case "three":
            return 3;
        case "four":
            return 4;
        case "five":
            return 5;
        case "six":
            return 6;
        case "seven":
            return 7;
        case "eight":
            return 8;
        case "nine":
            return 9;
        case "ten":
            return 10;
        case "eleven":
            return 11;
        case "twelve":
            return 12;
    }
}
?>
