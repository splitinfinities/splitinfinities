<?php while (have_posts()) : the_post(); ?>
	<section class="container title">
		<div class="column">
			<div class="content">
				<p><a href="../" class="<?php pjaxify(); ?>"><svg class="icon" style="width: 2.1788rem;height: 1.44rem;"><use xlink:href="#left-arrow" /></svg></a></p>
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
	</section>
	<section class="container single-content single_lists-<?php the_field('layout'); ?>">
		<?php if (get_field('layout') === "normal"): ?>
			<div class="column eight offset-by-four" itemscope="" itemtype="http://schema.org/Blog">
				<div class="content">
					<p class="subhead offset muted"><?php echo get_the_date('F j, Y'); ?></p>
					<?php the_field('right_content'); ?>
				</div>
			</div>
		<?php elseif (get_field('layout') === "list_and_content"): ?>
			<div class="column four list-group">
				<p class="h2"><strong><?php the_sub_field('name'); ?></strong></p>
				<?php if ( have_rows('left_list') ): ?>
					<?php while ( have_rows('left_list') ): the_row(); ?>
						<div class="list-item">
							<?php if ( get_sub_field('link') ): ?>
								<p class="h4"><a href="<?php the_sub_field('link'); ?>" target="_blank"><strong><?php the_sub_field('name'); ?></strong></a></p>
							<?php else: ?>
								<p class="h4"><strong><?php the_sub_field('name'); ?></strong></p>
							<?php endif; ?>
							<?php the_sub_field('details'); ?>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
			<div class="column eight">
				<div class="content">
					<?php the_field('right_content'); ?>
				</div>
			</div>
		<?php elseif (get_field('layout') === "grouped_list"): ?>
			<div class="column twelve" itemscope="" itemtype="http://schema.org/Blog">
				<div class="content container bleed one-half">
					<?php if ( have_rows('groups') ): ?>
						<?php while ( have_rows('groups') ): the_row(); ?>
							<div class="column list-group">
								<p class="h2"><strong><?php the_sub_field('name'); ?></strong></p>
								<?php if ( have_rows('list') ): ?>
									<?php while ( have_rows('list') ): the_row(); ?>
										<div class="list-item">
										<?php if ( get_sub_field('link') ): ?>
											<p class="h4"><a href="<?php the_sub_field('link'); ?>" target="_blank"><strong><?php the_sub_field('name'); ?></strong></a></p>
										<?php else: ?>
											<p class="h4"><strong><?php the_sub_field('name'); ?></strong></p>
										<?php endif; ?>
											<?php the_sub_field('details'); ?>
										</div>
									<?php endwhile; ?>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php else: ?>
			<div class="column eight offset-by-four" itemscope="" itemtype="http://schema.org/Blog">
				<div class="content">
					<p class="subhead offset muted"><?php echo get_the_date('F j, Y'); ?></p>
					<?php the_content(); ?>
				</div>
			</div>
		<?php endif; ?>

	</section>
<?php endwhile; ?>

<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('figure').on('click', function() {
			$(this).toggleClass('expanded')
		});
	});
</script>
<?php sendo()->capture_javascript_end(); ?>
