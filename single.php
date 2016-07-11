<article data-mip>
	<?php while (have_posts()) : the_post(); ?>
		<section class="container title">
			<div class="column">
				<div class="content kitchensink">
					<p><a href="../" class="<?php pjaxify(); ?>"><svg class="icon" style="width: 2.1788rem;height: 1.44rem;"><use xlink:href="#left-arrow" /></svg></a></p>
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
		</section>
		<section class="container single-content">
			<?php if ( get_field('layout_type') === 'normal_routing' ): ?>
			<div class="column eight offset-by-four" itemscope="" itemtype="http://schema.org/Blog">
				<div class="content kitchensink">
					<p class="subhead offset muted"><?php echo get_the_date('F j, Y'); ?></p>
					<?php if (get_field('content')): ?>
						<?php the_field('content'); ?>
					<?php else: ?>
						<?php the_content(); ?>
					<?php endif; ?>
				</div>
			</div>
			<?php elseif ( get_field('layout_type') === 'section_layout' ): ?>
				<?php if ( have_rows('page_sections') ): ?>
					<?php while ( have_rows('page_sections') ): the_row(); ?>
						<?php partial( 'sections/layout', get_row_layout() ); ?>
					<?php endwhile; ?>
				<?php else: ?>
					<?php partial( 'sections/layout', 'empty' ); ?>
				<?php endif; ?>
			<?php endif; ?>
		</section>
	<?php endwhile; ?>
</article>

<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('figure').on('click', function() {
			$(this).toggleClass('expanded')
		});
	});
</script>
<?php sendo()->capture_javascript_end(); ?>
