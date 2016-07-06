<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="layout-quote">
	<div class="container">
		<div class="column kitchensink anim fadeInUp delay-500">
			<div class="content container <?php echo get_sub_field('quote_type'); ?> <?php if( get_sub_field('quote_alignment')): ?>justify-center<?php endif; ?> <?php echo get_sub_field('quote_width'); ?>">
				<?php the_sub_field('quote_title'); ?>
				<?php if ( get_sub_field('quote_type')): ?>
					<span>
						<img src="<?php echo get_template_directory_uri(); ?>/assets/img/animated_pngs/lightning_bolt.png" class="apng-image" />
						<img src="<?php echo get_template_directory_uri(); ?>/assets/img/animated_pngs/lightning_bolt@2x.png" class="apng-image retina" />
					</span>
				<?php else: ?>
					<span></span>
				<?php endif; ?>
				<?php the_sub_field('quote_content'); ?>
			</div>
		</div>
	</div>
</section>
