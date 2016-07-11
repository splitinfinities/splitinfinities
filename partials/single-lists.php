<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="column post-preview <?php echo get_post_type(); ?><?php pjaxify(); ?>" itemscope="" itemtype="http://schema.org/BlogPosting">
	<div class="content">
		<span class="h3 title"><?php the_title(); ?></span>
		<span class="h3 the_icon"><svg class="icon muted flipped" style="width: 2.1788rem;height: 1.44rem;"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#left-arrow"></use></svg></span>
		<span class="h3 date muted" datetime="<?php echo get_the_date('U'); ?>" itemprop="datePublished"><?php echo get_the_date('F j'); ?></span>
	</div>
</a>
