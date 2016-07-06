<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="social_media container<?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?>" <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>>
	<div class="container one-third padding lop-off bleed">
	<?php $user = get_sub_field('user'); ?>
	<?php foreach (array(get_sub_field('first'), get_sub_field('second'), get_sub_field('third'), get_sub_field('fourth')) as $sub_field): ?>
		<?php if ($sub_field): ?>
	<?php $item = grab_users_preferred_content($sub_field, $user); ?>
		<a href="<?php echo $item->link; ?>"<?php echo ( $sub_field !== 'post' && $sub_field !== 'favorite_project' ) ? ' target="_blank" ' : '' ; ?>title="<?php echo $item->title; ?>" class="column<?php echo ( $sub_field == 'post' || $sub_field == 'favorite_project' ) ? pjaxify() : '' ; ?> <?php echo $sub_field; ?>">
			<?php if ($item->type === 'image'): ?>
				<div class="image">
				<?php if ( $sub_field == 'post' || $sub_field == 'favorite_project' ): ?>
					<?php echo do_shortcode('[smart_image image_id="' . $item->image_sm . '" zoom="false"]'); ?>
				<?php else: ?>
					<?php echo a_smart_image($item->image_sm, $item->image_md, $item->image_lg); ?>
				<?php endif; ?>
				</div>
			<?php elseif ($item->type === 'copy'): ?>
				<div class="copy center" style="background-color: #<?php echo $item->background_color; ?>;">
					<div class="background-image" style="background-image: url('<?php echo $item->background_image; ?>');"></div>
					<p class="h1 text-center"><?php echo $item->copy; ?></p>
				</div>
			<?php elseif ($item->type === 'interactive'): ?>
				<div class="interactive">
					<?php echo do_shortcode($item->code); ?>
				</div>
			<?php elseif ($item->type === 'video'): ?>
				<div class="video">
				<video preload="auto" width="800" height="800" autoplay loop muted poster="<?php echo $item->image_sm->url; ?>">
					<source src="<?php echo $item->video_mp4_url; ?>>" type="video/mp4" />
					<source src="<?php echo $item->video_webm_url; ?>" type="video/webm" />
				<?php if ( $sub_field == 'post' || $sub_field == 'favorite_project' ): ?>
					<?php echo do_shortcode('[smart_image image_id="' . $item->image_sm . '" zoom="false"]'); ?>
				<?php else: ?>
					<?php echo a_smart_image($item->image_sm, $item->image_md, $item->image_lg); ?>
				<?php endif; ?>
				</video>
				</div>
			<?php endif; ?>
			<div class="hover"><?php $svg_name = ( $sub_field == 'post' || $sub_field == 'favorite_project' ) ? 'ampersand' : $sub_field; partial('images/social', $svg_name); ?></div>
			<div class="descript kitchensink">
				<p class="h5"><?php echo $item->caption; ?></p>
			</div>
			<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="800" height="800" />
		</a>
	<?php endif; ?>
	<?php endforeach; ?>
	</div>
	<?php partial('social', 'team_connect'); ?>
</section>
