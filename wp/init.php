<?php

if (function_exists('get_field')) {
	/**
	 * Configuration values
	 */
	define('GOOGLE_ANALYTICS_ID', get_field('seo_google_analytics_ua_code', 'option')); // UA-XXXXX-Y (Note: Universal Analytics only, not Classic Analytics)
} else {
	define('GOOGLE_ANALYTICS_ID', ''); // UA-XXXXX-Y (Note: Universal Analytics only, not Classic Analytics)
}

/**
 * Initialize all of Neighborhood's stuff
 * @return null
 */
function neighborhood_setup() {
	add_theme_support('soil-clean-up');
	add_theme_support('soil-nav-walker');
	add_theme_support('soil-nice-search');
	add_theme_support('soil-jquery-cdn');
	add_theme_support('soil-relative-urls');


	// Make theme available for translation
	// Community translations can be found at https://github.com/neighborhood/neighborhood-translations
	load_theme_textdomain('neighborhood', get_template_directory() . '/lang');

	// Enable plugins to manage the document title
	// http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
	add_theme_support('title-tag');

	// Register wp_nav_menu() menus
	// http://codex.wordpress.org/Function_Reference/register_nav_menus
	register_nav_menus([
		'primary_navigation' => __('Primary Navigation', 'neighborhood')
	]);

	// Add post thumbnails
	// http://codex.wordpress.org/Post_Thumbnails
	// http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
	// http://codex.wordpress.org/Function_Reference/add_image_size
	add_theme_support('post-thumbnails');

	// Add post formats
	// http://codex.wordpress.org/Post_Formats
	add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);


	// Enable HTML5 markup support
	// http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
	add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);


	// Tell the TinyMCE editor to use a custom stylesheet
	add_editor_style('/assets/css/editor-style.css');

	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}

add_action('after_setup_theme', 'neighborhood_setup');

/**
 * Register sidebars
 * @return null
 */
function neighborhood_widgets_init() {
	register_sidebar([
		'name'          => __('Primary', 'neighborhood'),
		'id'            => 'sidebar-primary',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>'
	]);

	register_sidebar([
		'name'          => __('Footer', 'neighborhood'),
		'id'            => 'sidebar-footer',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>'
	]);
}

add_action('widgets_init', 'neighborhood_widgets_init');

/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar() {
	static $display;

	isset($display) || $display = !in_array(true, [
		// The sidebar will NOT be displayed if ANY of the following return true.
		// @link https://codex.wordpress.org/Conditional_Tags
		is_404(),
		is_front_page(),
		is_page_template('template-custom.php'),
	]);

	return apply_filters('neighborhood/display_sidebar', $display);
}
