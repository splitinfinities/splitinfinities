<?php

/**
 * Loads the scripts we need on first page load
 * @param  Array $assets Array of script uri's
 * @return null
 */
function just_enqueue_first_page_load($assets) {
	// embed all resources
	wp_enqueue_style('neighborhood_css', get_template_directory_uri() . $assets['main.css'], false, null);
	wp_enqueue_style('neighborhood_compatibility', get_template_directory_uri() . $assets['compatibility.css'], false, null);

	$css_files = sendo()->css;

	foreach ($css_files as $css) {
		wp_enqueue_style('neighborhood_'.basename($css, '.css'), $css, false, null);
	}


	/**
	 * jQuery is loaded using the same method from HTML5 Boilerplate:
	 * Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
	 * It's kept in the header instead of footer to avoid conflicts with plugins.
	 */
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', $assets['jquery'], array(), null, true);
		add_filter('script_loader_src', 'neighborhood_jquery_local_fallback', 10, 2);
	}

	if (is_single() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	wp_enqueue_script('modernizr', $assets['modernizr'], array(), null, true); // Modernizr
	wp_enqueue_script('detectizr', $assets['detectizr'], array(), null, true); // Browser Detection (IE, Edge, Etc)
	// wp_enqueue_script('greensock', $assets['greensock'], array(), null, true); // For animation

	// wp_enqueue_script('google-maps', $assets['google-maps'], array(), null, true);

	wp_enqueue_script('jquery');

	if (get_field('enable_ajax', 'options')) {
		wp_enqueue_script('jquery-pjax', $assets['jquery-pjax'], array(), null, true);
	}

	wp_enqueue_script('neighborhood_js', get_template_directory_uri() . $assets['app.js'], array(), null, true);
	wp_enqueue_script('neighborhood_analytics', get_template_directory_uri() . $assets['jquery.scrolldepth.js'], array(), null, true);
}

/**
 * Scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/assets/css/main.css
 *
 * Enqueue scripts in the following order:
 * 1. jquery-1.11.1.min.js via Google CDN
 * 2. /theme/assets/js/vendor/modernizr.min.js
 * 3. /theme/assets/js/scripts.js
 *
 * @return null
 */
function neighborhood_scripts() {

	$assets_basepath = '/assets_compiled';
	$styles_subdir = '/css';
	$scripts_subdir = '/js';

	$assets_json_path = get_template_directory() . $assets_basepath . '/assets.json';

	$production = file_exists($assets_json_path);

	/**
	 * The build task in Grunt renames production assets with a hash
	 * Read the asset names from /assets_compiled/assets.json
	 */
	$base_assets = array(
		'modernizr'           => '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',
		'detectizr'           => '//cdnjs.cloudflare.com/ajax/libs/detectizr/2.2.0/detectizr.min.js',
		'jquery'              => '//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
		'jquery-pjax'         => '//cdnjs.cloudflare.com/ajax/libs/jquery.pjax/1.9.6/jquery.pjax.min.js',
		'google-maps'         => '//maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true',
		'greensock'           => '//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.2/TweenMax.min.js',
	);

	if ($production) {
		$assets_json = json_decode( file_get_contents( $assets_json_path ), true );

		foreach($assets_json as $source_asset => $distributed_asset) {
			if ( strpos( $distributed_asset, '.css' ) > 0 ) {
				$base_assets[$source_asset] = $assets_basepath . $styles_subdir . '/' . $distributed_asset;
			} else if ( strpos( $distributed_asset, '.js' ) > 0 ) {
				$base_assets[$source_asset] = $assets_basepath . $scripts_subdir . '/' . $distributed_asset;
			}
		}
	} else {
		$base_assets['main.css'] = $assets_basepath . $styles_subdir . '/main.css';
		$base_assets['compatibility.css'] = $assets_basepath . $styles_subdir . '/compatibility.css';
		$base_assets['app.js'] = $assets_basepath . $scripts_subdir . '/app.js';
		$base_assets['element_queries.js'] = $assets_basepath . $scripts_subdir . '/element_queries.js';
		$base_assets['jquery.scrolldepth.js'] = $assets_basepath . $scripts_subdir . '/jquery.scrolldepth.js';
	}

	if ( pjaxify( true ) == "ajax" ) {
		if (isset($_SERVER['HTTP_X_PJAX']) && $_SERVER['HTTP_X_PJAX'] == 'true') {

			if (!is_admin() && current_theme_supports('jquery-cdn')) {
				wp_deregister_script('jquery');
			}
		} else {
			// embed resources that we need only on first load
			just_enqueue_first_page_load($base_assets);
		}
	} else {
		just_enqueue_first_page_load($base_assets);
	}
}

add_action('wp_enqueue_scripts', 'neighborhood_scripts', 100);

/**
 * Adds local fallback script (http://wordpress.stackexchange.com/a/12450)
 * @param  string $src    URI representation of the local jQuery to call
 * @param  string $handle custom namespace for jquery
 * @return string         URI of the path to a local version of jQuery
 */
function neighborhood_jquery_local_fallback($src, $handle = null) {
	static $add_jquery_fallback = true;

	if ($add_jquery_fallback) {
		echo '<script type="text/javascript">window.jQuery || document.write(\'<script type="text/javascript" src="' . get_template_directory_uri() . '/assets_compiled/js/jquery.js"><\/script>\')</script>' . "\n";
		$add_jquery_fallback = false;
	}

	if ($handle === 'jquery') {
		$add_jquery_fallback = true;
	}

	return $src;
}
add_action('wp_head', 'neighborhood_jquery_local_fallback');
