<?php $is_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="layout-simple_content<?php echo $is_dark; ?><?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?>" <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>>
	<div class="container <?php echo get_sub_field('content_width'); ?>">
		<div class="column">
			<div class="content kitchensink">
				<?php the_sub_field('content'); ?>
			</div>
		</div>
	</div>
</section>
