<?php
/**
 * AIGA Nebraska includes
 *
 * The $NEIGHBORHOOD_mandatory_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 */

$NEIGHBORHOOD_mandatory_includes = [
	'wp/init_plugins.php',            // Initial theme setup and constants
	'wp/init.php',                    // Initial theme setup and constants
	'wp/conf_routes.php',             // Reroute pages or archives
	'wp/conf_activation.php',         // Theme activation
	'wp/conf_scripts.php',            // Scripts and stylesheets
	'wp/etc_utils.php',               // Utility functions
	'wp/conf_post_formats.php',       // configures post format changes
	'wp/conf_post_types.php',         // Initiates custom post types
	'wp/conf_post_taxonomies.php',    // Initiates taxonomies
	'wp/conf_post_acf.php',           // Initiates default ACF fields
	'wp/init_wrapper.php',            // Theme wrapper class
	'wp/init_search.php',             // Search queries
	'wp/conf_images.php',             // Functions for images and custom sizes
	'wp/init_sidebar.php',            // Sidebar class
	'wp/init_walkers_nav.php',        // Custom walkers
	'wp/conf_admin.php',              // Admin config
	'wp/etc_extras.php',              // Custom functions
	'wp/init_sendo.php',              // SENDO
	'wp/init_pjax.php',               // PJAX settings and fixes
	'wp/conf_editor.php',             // Configuration settings for the Editor/TinyMCE
	'wp/conf_gallery.php',            // Markup changes for the media gallery functionality
];

$NEIGHBORHOOD_features_prepend = [
	'wp/api_mobiledetect.php',        // gets user agent string from request
	// 'wp/api_carbon.php',              // Carbon API - Helps with date/times.
	// 'wp/api_codepen.php',             // Codepen API
	// 'wp/api_dribbble.php',            // Dribbble API
	// 'wp/api_facebook.php',            // Facebook API
	// 'wp/api_vimeo.php',               // Vimeo API
	// 'wp/api_instagram.php',           // Instagram API
	// 'wp/api_medium.php',              // Medium API
	// 'wp/api_rss.php',                 // RSS API Provider
	// 'wp/api_twitter.php',             // Twitter API
	// 'wp/api_helpers.php',             // Helper functions that make the above API's usable.
];

$NEIGHBORHOOD_features_append = [
	// 'wp/api_carbon.php',              // Carbon - Makes dealing with date/time easier
	'wp/init_htmlcompression.php',    // HTML compression on every request
	'wp/conf_shortcodes.php',         // Contains shortcodes, like [smart_image]
	'wp/conf_prettify_acf.php',       // Contains the code to make flexible content fields look prettier.
];

$NEIGHBORHOOD_includes = array_merge($NEIGHBORHOOD_features_prepend, $NEIGHBORHOOD_mandatory_includes, $NEIGHBORHOOD_features_append);


foreach ($NEIGHBORHOOD_includes as $file) {
	if (!$filepath = locate_template($file)) {
		trigger_error(sprintf(__('Error locating %s for inclusion', 'NEIGHBORHOOD'), $file), E_USER_ERROR);
	}

	require_once $filepath;
}

unset($file, $filepath);

if (class_exists('SENDO')) {
	$sendo = new SENDO();
}
