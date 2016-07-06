<?php while (have_posts()) : the_post(); ?>
	<article>
		<?php if ( get_field('layout_type') === 'normal_routing' ): ?>
			<section class="container bleed">
					<div class="container wide">
						<div class="column six">
							<h1><?php the_title(); ?></h1>
					</div>
				</div>
			</section>
			<section class="container tiny">
				<div class="column kitchensink">
					<?php
					if (get_field('content')) {
						the_field('content');
					} else {
						the_content();
					}; ?>
				</div>
			</section>
		<?php elseif ( get_field('layout_type') === 'section_layout' ): ?>
			<?php if ( have_rows('page_sections') ): ?>
				<?php while ( have_rows('page_sections') ): the_row(); ?>
					<?php partial( 'sections/layout', get_row_layout() ); ?>
				<?php endwhile; ?>
			<?php else: ?>
				<?php partial( 'sections/layout', 'empty' ); ?>
			<?php endif; ?>
		<?php elseif ( get_field('layout_type') === 'custom_layout' ): ?>
			<?php global $wp_query; ?>
			<?php include ABSPATH . 'wp-content/custom/'.$wp_query->post->post_type.'-'.$wp_query->post->post_name.'.php'; ?>
			<?php sendo()->append_css('/wp-content/custom/'.$wp_query->post->post_type.'-'.$wp_query->post->post_name.'.css'); ?>
			<?php sendo()->append_javascript('/wp-content/custom/'.$wp_query->post->post_type.'-'.$wp_query->post->post_name.'.js'); ?>
		<?php endif; ?>
	</article>
<?php endwhile; ?>
