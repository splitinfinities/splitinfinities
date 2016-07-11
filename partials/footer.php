<footer class="content-info">
	<div class="container">
		<div class="column twelve">
			<div class="kitchensink">
				<hr />
			</div>
		</div>
	</div>
	<div class="container">
		<div class="column">
			<?php
				if (has_nav_menu('footer_navigation')) :
					wp_nav_menu(array('theme_location' => 'footer_navigation', 'walker' => new NEIGHBORHOOD_Nav_Walker(), 'menu_class' => 'unstyled inline'));
				endif;
			?>
		</div>
	</div>
	<div class="container">
		<div class="column twelve">
			<div class="kitchensink">
				<hr />
			</div>
		</div>
	</div>
	<div class="container">
		<div class="column four">
			<p class="muted"><small>William M. Riley</small></p>
			<p class="muted"><small><a href="/?feed=rss2&post_type[]=essays&post_type[]=notes&post_type[]=lists" target="_blank">RSS</a>  |  <a href="https://twitter.com/splitinfinities" target="_blank">Twitter</a>  |  <a href="https://facebook.com/splitinfinities" target="_blank">Facebook</a></small></p>
		</div>
		<div class="column four"></div>
		<div class="column four">
			<p class="muted text-right caf" data-func="contact_swap" data_open="intro"><small>Contact</small><small>will@splitinfinities.com</small></p>
		</div>
	</div>
	<div class="container">
		<div class="column">
			<?php echo do_shortcode('[smart_image image_id="108"]') ?>
		</div>
	</div>
</footer>
<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">
	λ.contact_swap = function(el) {
		if ($(el).attr('data_open') === 'intro') {
			$(el).attr('data_open', 'email');
		} else {
			$(el).attr('data_open', 'intro');
		}

		λ.contact_swap = null;
		$(el).removeClass('caf');
	};
</script>
<?php sendo()->capture_javascript_end(); ?>
