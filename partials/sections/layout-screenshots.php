<?php $is_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="layout-screenshots<?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?><?php echo $is_dark; ?>" <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>>
	<div class="container" data-count="<?php echo count(get_sub_field('screenshots')); ?>">
		<?php if ( have_rows('screenshots') ): ?>
			<?php while( have_rows('screenshots') ) : the_row(); ?>
				<div class="column three caf" data-func="screenshot_activate_this">
					<div class="content">
					<?php echo do_shortcode('[smart_image image_id="' . get_sub_field('screenshot') . '" width="'.$image['width'].'" height="'.$image['height'].'"' . $image_bleed . ']'); ?>
					</div>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>
