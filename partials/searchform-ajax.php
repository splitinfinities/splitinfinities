<form role="search" method="get" class="search-form search-form-ajax form-inline" action="<?= esc_url(home_url('/search/')); ?>">
	<label class="sr"><?php _e('Search for:', 'sage'); ?></label>
	<div class="input-group">
		<input type="search" value="<?php echo get_search_query(); ?>" name="s" class="search-field form-control" placeholder="<?php _e('Search', 'neighborhood'); ?> <?php bloginfo('name'); ?>" required>
		<span class="input-group-btn">
			<button type="submit" class="search-submit button grey"><?php _e('Search', 'neighborhood'); ?></button>
		</span>
		<a href="/search/" class="sr <?php pjaxify(); ?>" data-secret-link></a>
	</div>
</form>
<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">
	$('form.search-form-ajax').bind('submit', function(e) {
		e.preventDefault();

		var form_submitted = $(this);

		var term = $(this).find('[name="s"]').val();

		$('[data-secret-link]', $(this)).attr('href', '/search/' + term);

		$('[data-secret-link]', $(this)).click();

		$('button.search').click()
	});
</script>
<?php sendo()->capture_javascript_end(); ?>
