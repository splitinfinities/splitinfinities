<?php

use NEIGHBORHOOD\Setup;
use NEIGHBORHOOD\Wrapper;
use Carbon\Carbon as Carbon;


// Sets up SENDO
global $sendo;

$cached_time = Carbon::today();

// Sets up this page's cache.
// Be warned! If a query string exists, it doesn't consider that
// in the cache - so you could be served duplicate content.
$the_path_for_the_transient = strtok($_SERVER["REQUEST_URI"],'?');

// Use this code to consider a specific query string:
// if ($_GET['role_query']) {
// 	$the_path_for_the_transient = $the_path_for_the_transient . '/' . $_GET['role_query'];
// }

if (is_404()) {
	$the_path_for_the_transient = '404';
}

add_caldera_forms_assets_ajax();

if (get_field('enable_caching', 'option')) {
	$content_transient = get_transient( md5( $the_path_for_the_transient ) . '_content_' . $cached_time->timestamp );
	$sendo_transient = get_transient( md5( $the_path_for_the_transient ) . '_sendo_' . $cached_time->timestamp );
}

// If the caches are set, and the user isn't logged in,
// Set the $content variable and $sendo variable from the cache.
if ( $content_transient && $sendo_transient && ! is_user_logged_in( ) ) {
	$content = $content_transient;
	$sendo = $sendo_transient;
} else {
  // make an empty array for this page's keywords
	$keywords = [];

	if (get_field('seo_page_keywords', 'option') || get_field('seo_page_keywords')) {

		$keywords_to_get = (get_field('seo_page_keywords')) ? get_field('seo_page_keywords') : get_field('seo_page_keywords', 'option');

		foreach ($keywords_to_get as $key => $value) {
			$keywords[] = $value['keyword'];
		}
	}

	$page_title = ( get_field( 'seo_page_title' ) ) ? get_field('seo_page_title') : get_field('seo_page_title', 'options');
	$page_title = $sendo->page_title_treatment($page_title);

	$sendo->init(array(
		'title' => $page_title,
		'description' => ( get_field( 'seo_page_description' ) ) ? get_field('seo_page_description') : get_field('seo_page_description', 'option'),
		'image' => ( get_field( 'sdo_fallback_image' ) ) ? get_field('sdo_fallback_image') : get_field('sdo_fallback_image', 'option'),
		'url' =>  get_permalink(),
		'tags' => $keywords )
	);

	ob_start();
	include Wrapper\template_path();
	$content = ob_get_clean();
	$content = new WP_HTML_Compression($content);

	if ( get_field('enable_caching', 'option') ) {
		if ( ! is_user_logged_in( ) ) {
			set_transient( md5( $the_path_for_the_transient ) . '_content_' . $cached_time->timestamp, $content, 12 * HOUR_IN_SECONDS);
			set_transient( md5( $the_path_for_the_transient ) . '_sendo_' . $cached_time->timestamp, $sendo, 12 * HOUR_IN_SECONDS);
		} else {
			delete_transient( md5( $the_path_for_the_transient ) . '_content_' . $cached_time->timestamp);
			delete_transient( md5( $the_path_for_the_transient ) . '_sendo_' . $cached_time->timestamp);
		}
	}
}

/** Begin PJAX logic **/
if ( isset( $_SERVER['HTTP_X_PJAX'] ) ) :
  // If this is a pjax request
	if ( $_SERVER['HTTP_X_PJAX'] == 'true' ) {
		ob_start();
		$sendo->output('meta');
		$sendo->output('style');
		$sendo->output('scripts'); ?>
		<?php wp_footer(); ?>
		<main class="main" role="main" data-theclass="<?php echo pjax_body_class('content-wrapper'); ?>">
			<?php echo $content; ?>
		</main>
		<script type="text/javascript">
			$('body').attr('class', "<?php pjax_body_class('content-wrapper'); ?>");
			$('#wp-admin-bar-edit a', 'body').attr('href', '<?php echo html_entity_decode(get_edit_post_link()); ?>');
			console.warn('<?php echo get_num_queries(); ?> queries in <?php timer_stop(1); ?> seconds.');
		</script>
		<?php
		$sendo->output('prepend_captured_scripts');
		$sendo->output('append_captured_scripts');
		$this_ajax_page = ob_get_clean();
		$this_ajax_page = new WP_HTML_Compression($this_ajax_page);
		echo $this_ajax_page;
	}

	// If you want a slimmer ajax request, such as a request for raw json,
	// In the $.ajax function you make, inside the beforeSend setting, you
	// need to set the header HTTP_X_PJAX to false, do this
	// with xhr.setRequestHeader('X_PJAX', 'false')
	else {
		echo $content;
	}

	else: ?>
	<?php ob_start(); ?>
	<!doctype html>
	<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<?php $sendo->output('meta'); ?>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
		<?php /* <link rel="prefetch" href="<?php echo get_template_directory_uri(); ?>/assets/img/location-cityscape-spacer.png" /> */ ?>
		<?php
		// Removes wordpress's title from the wp_head code.
		ob_start();
		wp_head();
		$wordpress_head = ob_get_clean();
		$wordpress_head = preg_replace('/<title\b[^>]*>(.*?)<\/title>/i', '', $wordpress_head);
		echo $wordpress_head;
		?>
	</head>
	<body <?php body_class('content-wrapper'); ?>>
		<?php partial('svg-objects'); ?>
		<?php partial('header'); ?>
		<?php partial('loader'); ?>

		<div id="main" role="document">
			<main class="main" role="main" data-theclass="<?php echo pjax_body_class('content-wrapper'); ?>">
				<?php echo $content; ?>
			</main><!-- /.main -->
		</div><!-- /.wrap -->
		<?php partial('footer'); ?>

		<div id="center"></div>
		<div id="full"><?php partial('animated-canvas'); ?></div>

		<?php $sendo->output('prepend_captured_scripts'); ?>
		<?php wp_footer(); ?>
		<?php $sendo->output('scripts'); ?>
		<?php $sendo->output('append_captured_scripts'); ?>

		<script type="text/javascript">
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo GOOGLE_ANALYTICS_ID; ?>', 'auto');
			ga('send', 'pageview');
		</script>

		<script type="text/javascript">
			var $buoop = {vs:{i:9,f:15,o:12.1,s:7},c:2};
			function $buo_f(){
				var e = document.createElement("script");
				e.src = "//browser-update.org/update.js";
				document.body.appendChild(e);
			};
			try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
			catch(e){window.attachEvent("onload", $buo_f)}
		</script>
	</body>
	</html>
	<?php $this_full_page = ob_get_clean(); ?>
	<?php $this_full_page = new WP_HTML_Compression($this_full_page); ?>
	<?php echo $this_full_page; ?>
	<script type="text/javascript">
		console.warn('<?php echo get_num_queries(); ?> queries in <?php timer_stop(1); ?> seconds.');

		console.log('<?php echo ( get_field( 'enable_caching', 'option' ) ) ? 'Caching is enabled' : 'Caching is disabled'; ?>');

		;(function css_performance() {
			window.onload = function() {
				setTimeout(function() {
					var t = performance.timing;
					console.warn("Speed of selection is: " + (t.loadEventEnd - t.responseEnd) + " milliseconds");
				}, 0);
			};
		})();
	</script>
<?php endif; ?>
