<?php
	global $i;

	$image_field = get_field('preview_image');
	$vertical_image_field = get_field('portrait_preview_image');
	$post_image_field = (!empty(get_field('page_sections')[0]['hero_image'])) ? get_field('page_sections')[0]['hero_image'] : get_field('preview_image');
	$background_color = (!empty(get_field('page_sections')[0]['background_color'])) ? get_field('page_sections')[0]['background_color'] : get_field('brand_primary_color', 'options');


	$background_color = (!empty(get_field('image_preload_color', $image_field))) ? get_field('image_preload_color', $image_field) : get_field('brand_image_preloading_color', 'options');
	$background_color = ( $background_color == '' ) ? '#ECF1F3': $background_color;

?>

<a href="<?php the_permalink(); ?>" class="column explore-preview <?php echo get_post_type(); ?> anim fadeInUp delay-<?php echo ($i * 100); ?><?php pjaxify(); ?>" itemscope="" itemtype="http://schema.org/BlogPosting">
	<div class="preview-image" style="background-color: <?php echo $background_color; ?>">
		<div class="image anim fadeIn delay-500">
			<?php echo do_shortcode('[smart_image image_id="' . $image_field . '" zoom="false" class="landscape"]'); ?>
			<?php echo do_shortcode('[smart_image image_id="' . $vertical_image_field . '" zoom="false" class="portrait"]'); ?>
		</div>
		<?php /*<img src="<?php echo get_template_directory_uri(); ?>/assets/img/aspect/blog-preview.gif" width="20" height="11" />*/ ?>
		<img src="data:image/gif;base64,R0lGODlhAgABAIAAAP///wAAACH5BAAAAAAALAAAAAACAAEAAAICBAoAOw==" width="2" height="1" class="spacer landscape-spacer" />
		<img src="data:image/gif;base64,R0lGODlhFQAMAIAAAP///wAAACH5BAAAAAAALAAAAAAVAAwAAAINhI+py+0Po5y02otzLgA7" width="7" height="4" class="spacer landscape-spacer-short" />
		<img src="data:image/gif;base64,R0lGODlhAwAEAIAAAP///wAAACH5BAAAAAAALAAAAAADAAQAAAIDhI9WADs=" width="3" height="4" class="spacer portrait-spacer-short" />
	</div>
	<div class="content kitchensink">
		<?php if ( get_sub_field( 'content' ) ): ?>
			<?php the_sub_field( 'content' ) ?>
		<?php else: ?>
			<?php $term = wp_get_object_terms( $post->ID, 'blog_categories', array( 'fields' => 'names' ) )[0]; ?>
			<?php if ($term): ?>
				<p class="h6 anim fadeInUp delay-600"><?php echo $term; ?></p>
			<?php endif; ?>
			<h2 class="anim fadeInLeft delay-700 h1" itemprop="name headline" title="<?php echo word_wrapper(get_the_title($post->ID)); ?>"><?php the_title(); ?></h2>
			<p class="anim fadeInLeft delay-800"><?php echo substr(get_the_excerpt(), 0, 140); ?></p>
		<?php endif; ?>
	</div>
</a>
