<?php $is_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="page-list<?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?><?php echo $is_dark; ?>" <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>>
	<div class="container <?php echo get_sub_field('column_count'); ?><?php echo (get_sub_field('has_padding')) ? ' padding' : ''; ?><?php echo (get_sub_field('is_full_page')) ? ' bleed' : ''; ?>">

		<?php if ( get_sub_field( 'predefined_items' ) ): ?>
			<?php $query_type = trim(get_sub_field( 'predefined_items' ));

			if ( $query_type === 'work' ):
				$args = array(
					'post_type' => 'work',
					'posts_per_page' => 24,
					'orderby' => 'menu_order',
					'tax_query' => array(
						'relation' => 'OR',
						array(
							'taxonomy' => 'work_type',
							'field' => 'slug',
							'terms' => 'case-study',
							),
						array(
							'taxonomy' => 'work_type',
							'field' => 'slug',
							'terms' => 'portfolio',
							)
						)
				);
			elseif ( $query_type === 'explore' ):
				$args = array(
					'post_type' => 'explore',
					'posts_per_page' => 24,
					'orderby' => 'menu_order',
				);
			elseif ( $query_type === 'team_members' ):
				$args = array(
					'post_type' => 'team',
					'posts_per_page' => 12,
					'orderby' => 'menu_order',
					'order' => 'ASC'
				);
			elseif ( $query_type === 'services' ):
				$args = array(
					'post_type'      => 'page',
					'posts_per_page' => -1,
					'post_parent'    => 142, // hardcoded value of the services page so we don't query the database even more.
					'order'          => 'ASC',
					'orderby'        => 'menu_order'
				);
			endif;

			$our_posts = new WP_Query( $args );

			while ( $our_posts->have_posts() ) : $our_posts->the_post(); ?>
			<?php partial('sections/content/post', get_post_type()); ?>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
	<?php endif; ?>
	<?php if ( $query_type === 'other' ): ?>
		<?php if ( have_rows('items') ): ?>
			<?php while( have_rows('items') ) : the_row(); ?>
				<?php partial('sections/content/post', get_post_type()); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
</section>
<?php wp_reset_postdata(); ?>
