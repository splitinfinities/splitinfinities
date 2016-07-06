<?php global $page_list_posts; ?>
<?php global $page_list_title; ?>
<?php if ($page_list_posts->have_posts()): ?>
<section class="snippets-examples">
	<div class="container one-half padding" data-remainer="<?php echo count($page_list_posts->posts); ?>">

		<div class="bar anim fadeIn delay-300">
			<div class="bar-title anim fadeInLeft delay-600">
				<p class="anim fadeInLeft delay-700"><?php echo $page_list_title; ?></p>
			</div>
		</div>

		<?php while ( $page_list_posts->have_posts() ) : $page_list_posts->the_post(); ?>
			<?php /* Grab the post-works.php partial */ ?>
			<?php partial('sections/content/post', 'snippet'); ?>
		<?php endwhile; ?>

		<?php wp_reset_query(); wp_reset_postdata(); ?>
	</div>
</section>
<?php endif; ?>
