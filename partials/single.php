<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="column post-preview <?php echo get_post_type(); ?><?php pjaxify(); ?>" itemscope="" itemtype="http://schema.org/BlogPosting">
	<div class="content">
		<span class="h2" datetime="<?php echo get_the_date('U'); ?>" itemprop="datePublished"><strong><?php echo get_the_date('F j'); ?></strong></span>
		<span><strong itemprop="name headline"><?php the_title(); ?>:</strong> <?php the_excerpt(); ?></span>
	</div>
</a>
