<?php

/**
 * Makes wordpress aware of custom variables
 * @return null
 */
function custom_rewrite_tag() {
  add_rewrite_tag('%work_category%', '([^&]+)');
  add_rewrite_tag('%work_paged%', '([^&]+)');
}

add_action('init', 'custom_rewrite_tag', 10, 0);

/**
 * Rewrites the routes based on regex, used for pretty url rewrites.
 * @return null
 */
function neighborhood_route_rewrites() {
	// add_rewrite_rule('^work/in-the-press','index.php?post_type=blog&taxonomy=blog_category&term=in-the-press','top');
	// add_rewrite_rule("^work/([^/]+)/([^/]+)/?",'index.php?post_type=case-studies&news_category=$matches[1]&news=$matches[2]','top');


	add_rewrite_rule("^about/careers/designer",'index.php?page_id=3532','top');
	add_rewrite_rule("^about/careers/developer",'index.php?page_id=3534','top');
	add_rewrite_rule("^about/careers",'index.php?page_id=3530','top');

	add_rewrite_rule("^about/([^/]*)/posts/feed/(feed|rdf|rss|rss2|atom)/?$",'index.php?author_name=$matches[1]&feed=$matches[2]','top');
	add_rewrite_rule("^about/([^/]*)/posts/(feed|rdf|rss|rss2|atom)/?$",'index.php?author_name=$matches[1]&feed=$matches[2]','top');
	add_rewrite_rule("^about/([^/]*)/posts/?$",'index.php?author_name=$matches[1]&paged=$matches[2]','top');
	add_rewrite_rule("^about/([^/]*)/posts",'index.php?author_name=$matches[1]','top');

	add_rewrite_rule("^blog/insights/page/([0-9]+)",'index.php?post_type=blog&blog_categories=insights&paged=$matches[1]','top');
	add_rewrite_rule("^blog/insights",'index.php?post_type=blog&blog_categories=insights','top');
	add_rewrite_rule("^blog/studio/page/([0-9]+)",'index.php?post_type=blog&blog_categories=studio&paged=$matches[1]','top');
	add_rewrite_rule("^blog/studio",'index.php?post_type=blog&blog_categories=studio','top');
	add_rewrite_rule("^blog/work/page/([0-9]+)",'index.php?post_type=blog&blog_categories=work&paged=$matches[1]','top');
	add_rewrite_rule("^blog/work",'index.php?post_type=blog&blog_categories=work','top');

	add_rewrite_rule('^work/category/([^/]*)/page/([0-9]+)','index.php?post_type=work&work_category=$matches[1]&work_paged=$matches[2]','top');
	add_rewrite_rule('^work/category/([^/]*)','index.php?post_type=work&work_category=$matches[1]','top');
	add_rewrite_rule('^work/category','index.php?post_type=work','top');

	add_rewrite_rule('^work/page/([0-9]+)','index.php?post_type=work&work_paged=$matches[1]','top');

	add_rewrite_rule('^contact/form','index.php?page_id=3523','top');

}

add_action('init', 'neighborhood_route_rewrites', 10, 0);
