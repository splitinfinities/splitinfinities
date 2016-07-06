<form role="search" method="get" class="search-form form-inline" action="<?= esc_url(home_url('/')); ?>">
	<label class="sr"><?php _e('Search for:', 'sage'); ?></label>
	<div class="input-group">
		<input type="search" value="<?php echo get_search_query(); ?>" name="s" class="search-field form-control" placeholder="<?php _e('Search', 'neighborhood'); ?> <?php bloginfo('name'); ?>" required>
		<span class="input-group-btn">
			<button type="submit" class="search-submit button grey"><?php _e('Search', 'neighborhood'); ?></button>
		</span>
	</div>
</form>
