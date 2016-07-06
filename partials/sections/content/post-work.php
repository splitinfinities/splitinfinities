<?php
	/* var $is_this_sticky is used if this post preview is the first item on the home page. */
	global $i;
	$image_field = get_field('preview_image');
	$background_color = (!empty(get_field('page_sections')[0]['background_color'])) ? get_field('page_sections')[0]['background_color'] : get_field('brand_primary_color', 'options');
	$background_color = (!empty(get_field('image_preload_color', $image_field))) ? get_field('image_preload_color', $image_field) : get_field('brand_image_preloading_color', 'options');
	$background_color = ( $background_color == '' ) ? '#ECF1F3': $background_color;
?>

<a href="<?php the_permalink(); ?>" class="column work-preview anim fadeInUp delay-<?php echo (($i + 1) * 150); ?><?php pjaxify(); ?>" itemscope="" itemtype="http://schema.org/BlogPosting" style="background-color: <?php echo $background_color; ?>">
	<div class="preview-image anim fadeIn delay-400" <?php echo responsive_bg($image_field); ?>></div>
	<div class="content kitchensink">
		<?php if ( get_sub_field( 'content' ) ): ?>
			<?php the_sub_field( 'content' ) ?>
		<?php else: ?>
			<span class="h1" itemprop="name headline" title="<?php echo word_wrapper(get_the_title($post->ID), 3, 2); ?>"><?php the_title(); ?></span>
			<span class="h6"><?php echo substr(get_the_excerpt(), 0, 140); ?></span>
		<?php endif; ?>
	</div>
	<img src="data:image/gif;base64,R0lGODlhFAAXAIAAAP///wAAACH5BAAAAAAALAAAAAAUABcAAAIThI+py+0Po5y02ouz3rz7D4ZdAQA7" class="spacer" />
</a>
