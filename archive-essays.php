<?php sendo()->set_title(get_post_type_object(get_post_type())->labels->name); ?>
<div class="container">
	<div class="column twelve" itemscope="" itemtype="http://schema.org/Blog">
		<section class="container bleed">
		<?php $current_year = ''; ?>
			<?php while (have_posts()) : the_post(); ?>
				<?php if ($current_year != get_the_date('Y')): ?>
					<?php $current_year = get_the_date('Y'); ?>
					<div class="current-year column twelve"><span class="h3"><?php echo get_the_date('Y'); ?></span></div>
				<?php endif; ?>
				<?php partial('single', get_post_type()); ?>
			<?php endwhile; ?>
		</section>
	</div>
</div>
