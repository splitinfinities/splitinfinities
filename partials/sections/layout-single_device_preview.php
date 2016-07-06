<?php $is_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
<?php $background_image = wp_get_attachment_image_src(get_sub_field('background_image'), 'full')[0]; ?>
<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="single_device_preview <?php echo $is_dark; ?> <?php echo get_sub_field('include_details_section'); ?> <?php echo get_sub_field('device_overflow'); ?> <?php echo get_sub_field('device_orientation'); ?><?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?>" style="<?php if (get_sub_field('background_color')): ?>background-color:<?php echo get_sub_field('background_color'); ?>;<?php endif; ?><?php if ($background_image): ?>background-image:url('<?php echo $background_image; ?>');<?php endif; ?>">
	<div class="container">
		<div class="column">
			<div class="device <?php echo get_sub_field('device_type'); ?> <?php echo get_sub_field('device_orientation'); ?>">
			<?php if ( in_array( get_sub_field('device_type'), array( 'macbook') ) ): ?>
				<img src="/wp-content/themes/aiga-nebraska/assets/img/devices/<?php echo get_sub_field('device_type').'-'.get_sub_field('device_color'); ?>.png" />
			<?php elseif ( in_array( get_sub_field('device_type'), array( 'macbook_pro', 'imac' ) ) ): ?>
				<img src="/wp-content/themes/aiga-nebraska/assets/img/devices/<?php echo get_sub_field('device_type'); ?>.png" />
			<?php else: ?>
				<img src="/wp-content/themes/aiga-nebraska/assets/img/devices/<?php echo get_sub_field('device_type').'-'.get_sub_field('device_color').'-'.get_sub_field('device_orientation'); ?>.png" />
			<?php endif; ?>
				<div class="device-content">
					<?php
						switch (get_sub_field('device_type')):
							case 'iphone5s':
								$width = (get_sub_field('device_orientation') === 'vertical') ? '658x1156' : '1156x658'; /* confirmed */
							break;
							case 'iphone6s':
								$width = (get_sub_field('device_orientation') === 'vertical') ? '750x1254' : '1254x750'; /* confirmed */
							break;
							case 'iphone6s_plus':
								$width = (get_sub_field('device_orientation') === 'vertical') ? '828x1472' : '1472x828'; /* confirmed */
							break;
							case 'ipadmini4':
								$width = (get_sub_field('device_orientation') === 'vertical') ? '922x1228' : '1228x922'; /* confirmed */
							break;
							case 'ipadair2':
								$width = (get_sub_field('device_orientation') === 'vertical') ? '1132x1510' : '1510x1132';
							break;
							case 'ipadpro':
								$width = (get_sub_field('device_orientation') === 'vertical') ? '1182x1580' : '1580x1182';
							break;
							case 'macbook':
								$width = '1700x1060';
							break;
							case 'macbook_pro':
								$width = '1700x1060';
							break;
							case 'imac':
								$width = '1700x1060';
							break;
							default:
								$width = '500x500';
							break;
						endswitch;
					?>

					<?php if( get_sub_field('content_type') === "image" ): ?>
						<?php if( get_sub_field('image') ): ?>
							<?php $deviceimage = wp_get_attachment_image_src( get_sub_field('image'), 'full'); ?>
							<img src="<?php echo $deviceimage[0]; ?>" />
						<?php else: ?>
							<img src="http://www.placehold.it/<?php echo $width; ?>&amp;text=<?php echo get_sub_field('device_type'); ?>" alt="" />
						<?php endif; ?>
					<?php elseif( get_sub_field('content_type') === "video" ): ?>
						<?php echo include_video(get_sub_field('video_vimeo_link'), get_sub_field('image')); ?>
					<?php else: ?>
						<img src="http://www.placehold.it/<?php echo $width; ?>&amp;text=Fail-Faillback" alt="" />
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php if (get_sub_field('include_details_section') === "left_device" || get_sub_field('include_details_section') === "right_device"): ?>
			<div class="column<?php echo (get_sub_field('include_details_section') === "right_device") ? ' last' : ' first'; ?>">
				<div class="content kitchensink">
					<?php the_sub_field('details_about_this'); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php if (get_sub_field('include_details_section') === "below_device"): ?>
		<div class="container">
			<div class="column kitchensink">
				<?php the_sub_field('details_about_this'); ?>
			</div>
		</div>
	<?php endif; ?>
</section>
