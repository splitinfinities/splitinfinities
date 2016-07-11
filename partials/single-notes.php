<div href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="column post-preview six <?php echo get_post_type(); ?><?php pjaxify(); ?>" itemscope="" itemtype="http://schema.org/BlogPosting">
	<div class="content">
		<span class="h2 muted" datetime="<?php echo get_the_date('U'); ?>" itemprop="datePublished"><?php echo get_the_date('F j'); ?></span>
		<span class="copy"><?php if (get_field('content')): ?> <?php the_field('content'); ?> <?php else: ?> <?php the_content(); ?> <?php endif; ?></span>
	</div>
</div>
