<?php

/**
 * Redirects search results from /?s=query to /search/query/, converts %20 to +
 *
 * You can enable/disable this feature in functions.php (or lib/config.php if you're using Roots):
 * add_theme_support('soil-nice-search');
 */
function soil_nice_search_redirect() {
  global $wp_rewrite;
  if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) {
    return;
  }

  $search_base = $wp_rewrite->search_base;
  if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
    wp_redirect(home_url("/{$search_base}/" . urlencode(get_query_var('s'))));
    exit();
  }
}
add_action('template_redirect', 'soil_nice_search_redirect');

/***
 * Limit search queries to specific post types
 **/
function searchfilter( $query ) {
  if ( $query->is_search && !is_admin() ) {
    $query->set( 'post_type', array( 'page', 'whatever', 'blog' ) );
  }

  return $query;
}

add_filter('pre_get_posts','searchfilter');


/***
 * Order search queries by post type
 **/
function neighborhood_sort_custom( $orderby, $query ) {
    global $wpdb;

    if ( ! is_admin() && is_search() ) {
        $orderby =  $wpdb->prefix."posts.post_type ASC, {$wpdb->prefix}posts.post_date ASC";
    }

    return  $orderby;
}

add_filter('posts_orderby','neighborhood_sort_custom', 10, 2);

?>
