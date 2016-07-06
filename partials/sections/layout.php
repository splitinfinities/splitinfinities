<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="container bleed<?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?>" layout="<?php echo get_field('layout_type'); ?>" <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>>
	<div class="container">
		<div class="column">
			<div class="content">
				<!-- You can only reach this page if the 'layout-<?php echo get_row_layout(); ?>' isn't named correctly
				     or the file doesn't exist yet. Please check your spelling and make sure the file exists. -->
				<p>This section needs to be developed!</p>
			</div>
		</div>
	</div>
</section>
