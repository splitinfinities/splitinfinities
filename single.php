<?php while (have_posts()) : the_post(); ?>
	<section class="container title">
		<div class="column">
			<div class="content">
				<p><a href="../" class="<?php pjaxify(); ?>"><svg class="icon" style="width: 2.1788rem;height: 1.44rem;"><use xlink:href="#left-arrow" /></svg></a></p>
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
	</section>
	<section class="container single-content">
		<div class="column eight offset-by-four" itemscope="" itemtype="http://schema.org/Blog">
			<div class="content">
				<p class="subhead offset muted"><?php echo get_the_date('F j, Y'); ?></p>
				<?php the_content(); ?>
			</div>
		</div>
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
