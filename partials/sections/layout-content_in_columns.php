<?php $is_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
<?php $background_image = wp_get_attachment_image_src(get_sub_field('background_image'), 'full')[0]; ?>
<?php $section_name = get_sub_field('section_name'); ?>
<section <?php if ($section_name): ?>id="<?php echo $post->post_name.'-'.sanitize_title($section_name); ?>"<?php endif; ?> class="layout-multi_column<?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?><?php echo $is_dark; ?>" style="<?php if (get_sub_field('background_color')): ?>background-color:<?php echo get_sub_field('background_color'); ?>; <?php endif; ?><?php if ($background_image): ?>background-image:url('<?php echo $background_image; ?>');<?php endif; ?>">
	<?php if (get_sub_field('lead_in_content')): ?>
		<div class="container lead-in-content tiny anim fadeInUp delay-200">
			<div class="column twelve">
				<div class="content kitchensink">
					<?php the_sub_field('lead_in_content'); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php $column_count = get_sub_field('column_count'); ?>
	<?php $breathing_room = get_sub_field('breathing_room'); ?>
	<?php if ($column_count !== 'mosic'): ?>
	<div class="container anim fadeIn delay-300 <?php echo $column_count; ?><?php echo (get_sub_field('has_padding')) ? ' padding' : ''; ?><?php echo (get_sub_field('is_full_page')) ? ' bleed' : ''; ?>">
	<?php endif; ?>
		<?php $has_zooming = (get_sub_field('has_zooming')) ? 'zoom="true"' : ''; ?>
		<?php $has_bleed = get_sub_field('is_full_page'); ?>
		<?php if ( have_rows('media') ): ?>
			<?php $total_column_count = 0; ?>
			<?php while( have_rows('media') ) : the_row(); ?>
				<?php if ($column_count === 'mosic'): ?>
					<?php if ($total_column_count % 12 === 0): ?>
						<div class="container <?php echo $column_count; ?> <?php echo $breathing_room; ?><?php echo (get_sub_field('has_padding')) ? ' padding' : ''; ?><?php echo ($has_bleed) ? ' bleed' : ''; ?>">
					<?php endif; ?>
				<?php endif; ?>
				<?php $is_column_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
				<?php $column_span = ( is_array(get_sub_field( 'column_span' )) ) ? get_sub_field( 'column_span' )[0] : get_sub_field( 'column_span' ) ; ?>
					<div class="column <?php echo get_sub_field('content_type'); ?><?php echo ($column_span) ? ' '.$column_span : ''; ?><?php echo $is_column_dark; ?>" <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>>
					<?php if (get_sub_field('content_type') === 'copy'): ?>
						<div class="content kitchensink anim fadeIn delay-<?php echo ($total_column_count % 12) * 50; ?>">
							<?php echo get_sub_field('copy'); ?>
						</div>
					<?php elseif (get_sub_field('content_type') === 'image'): ?>
						<div class="content">
							<?php $image = wp_get_attachment_metadata(get_sub_field('media_id')); ?>
							<?php $image_bleed = ($has_bleed) ? ' bleed="true"' : ''; ?>
							<?php echo do_shortcode('[smart_image image_id="' . get_sub_field('media_id') . '" ' . $has_zooming . ' width="'.$image['width'].'" height="'.$image['height'].'"' . $image_bleed . ']'); ?>

						</div>
					<?php elseif (get_sub_field('content_type') === 'video'): ?>
						<div class="content anim fadeIn delay-<?php echo ($total_column_count % 12) * 50; ?>">
							<?php
								$video_actions = array();

								if ( get_sub_field( 'video_autoplay' ) ) {
									$video_actions[] = 'autoplay' ;
								}

								if ( get_sub_field( 'video_muted' ) ) {
									$video_actions[] = 'muted' ;
								}

								if ( get_sub_field( 'video_loop' ) ) {
									$video_actions[] = 'loop' ;
								}

								if ( get_sub_field( 'video_show_controls' ) ) {
									$video_actions[] = 'controls' ;
								}
							?>
							<?php echo include_video(get_sub_field('video_vimeo_link'), get_sub_field('media_id'), $video_actions); ?>
						</div>
					<?php elseif (get_sub_field('content_type') === 'custom'): ?>
						<?php $column_name = get_sub_field( 'column_name' ); ?>
						<div id="<?php echo $post->post_name . '-' . sanitize_title( $section_name ) . '-' . sanitize_title( $column_name ); ?>" class="content anim fadeIn delay-<?php echo ($total_column_count % 12) * 50; ?>">
						<?php
						if (!include 'custom/' . $post->post_name . '-' . sanitize_title( $section_name ) . '-' . sanitize_title( $column_name ) . '.php') {
							include 'custom/' . $post->post_name . '-' . sanitize_title( $section_name ) . '-' . sanitize_title( $column_name ) . '.php';
						} ?>
						</div>
					<?php endif; ?>
					</div>
				<?php if ($column_count === 'mosic'): ?>
					<?php $total_column_count = $total_column_count + convert_to_number($column_span); ?>
					<?php if ($total_column_count % 12 === 0): ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
		<?php endwhile; ?>
			<?php endif; ?>

	<?php if (get_sub_field('lead_out_content')): ?>
		<div class="container lead-out-content tiny anim fadeIn delay-400">
			<div class="column twelve">
				<div class="content kitchensink">
					<?php the_sub_field('lead_out_content'); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
