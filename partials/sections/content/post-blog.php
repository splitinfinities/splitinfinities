<?php
	/* var $is_this_sticky is used if this post preview is the first item on the home page. */
	global $is_this_sticky;
	global $i;

	$image_field = get_field('preview_image');
	$vertical_image_field = get_field('portrait_preview_image');
	$post_image_field = (!empty(get_field('page_sections')[0]['hero_image'])) ? get_field('page_sections')[0]['hero_image'] : get_field('preview_image');
	$background_color = (!empty(get_field('page_sections')[0]['background_color'])) ? get_field('page_sections')[0]['background_color'] : get_field('brand_primary_color', 'options');

	// $image_field = ($is_this_sticky) ? $post_image_field : $image_field;

?>

<a href="<?php the_permalink(); ?>" class="column blog-preview anim fadeInUp delay-<?php echo ($i * 100); ?><?php echo ($is_this_sticky) ? ' sticky' : ''; ?><?php pjaxify(); ?>" itemscope="" itemtype="http://schema.org/BlogPosting">
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
			<p class="h6 anim fadeInUp delay-600"><?php echo wp_get_object_terms( $post->ID, 'blog_categories', array( 'fields' => 'names' ) )[0]; ?></p>
			<h2 class="anim fadeInLeft delay-700 h1" itemprop="name headline" spaces="<?php echo ($is_this_sticky) ? '2' : '1'; ?>" title="<?php echo word_wrapper(get_the_title($post->ID), 3, (($is_this_sticky) ? 2 : 1) ); ?>"><?php the_title(); ?></h2>
			<?php /* This shows the Author and post date
			<?php $author_link ='/about/' . get_the_author_meta('user_login')  . '/posts'; ?>
			<span class="byline anim fadeInLeft delay-800"><em>by <span itemprop="author" itemscope="" itemtype="http://schema.org/Person"><a href="<?php echo $author_link; ?>" class="<?php pjaxify(); ?>" itemprop="name"><?php the_author(); ?></a></span></em> / <span datetime="<?php echo get_the_date('U'); ?>" itemprop="datePublished"><?php echo get_the_date(); ?></span></span>
			<?php if ($is_this_sticky): ?>
				<p class="cta anim fadeInLeft delay-900"><a href="<?php the_permalink(); ?>" class="<?php pjaxify(); ?>">Read more</a></p>
			<?php endif; ?>
			*/ ?>
			<p class="anim fadeInLeft delay-800"><?php echo substr(get_the_excerpt(), 0, 140); ?>&hellip;</p>
		<?php endif; ?>
	</div>
</a>
