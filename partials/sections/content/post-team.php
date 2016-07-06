<?php
	$image_field = get_field('preview_image');
?>

<a href="<?php the_permalink(); ?>" class="column anim fadeIn delay-400 team-preview<?php pjaxify(); ?>">
	<div class="employee-info-overlay">
		<div class="content">
			<?php $user = get_user_by( 'slug', $post->post_name ); ?>
			<p class="h3"><?php echo $user->display_name; ?></p>
			<p class="h6"><?php echo get_field('position'); ?></p>
		</div>
	</div>

	<div class="preview-image anim delay-500">
		<div class="image">
			<?php echo do_shortcode('[smart_image image_id="' . $image_field . '" zoom="false"]'); ?>
		</div>
	</div>
	<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="800" height="800" />
</a>
