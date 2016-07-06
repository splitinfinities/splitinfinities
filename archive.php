<?php sendo()->set_title(get_post_type_object(get_post_type())->labels->name); ?>
<div class="container">
	<aside class="column four">
		<ul class="unstyled">
			<?php $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '".get_post_type()."' ORDER BY post_date DESC"); ?>
			<?php $i = 1; ?>
			<?php $j = 1; ?>
			<?php foreach($years as $year): ?>
				<li class="year-container<?php echo ($i === 1) ? ' active' : ''; ?>">
					<a class="h2"><strong><?php echo $year; ?></strong></a>
					<ul class="archive-sub-menu unstyled">

						<?php $months = $wpdb->get_col("SELECT DISTINCT MONTH(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '".get_post_type()."' AND YEAR(post_date) = '".$year."' ORDER BY post_date DESC");
						foreach($months as $month) { ?>
						<li>
							<a class="subhead scrollto<?php echo ($j === 1) ? ' active' : ''; ?>" href="#<?php echo strtolower(date( 'F', mktime(0, 0, 0, $month) ) . '-' . $year); ?>" data-scrollto="<?php echo strtolower(date( 'F', mktime(0, 0, 0, $month) ) . '-' . $year); ?>" title="Scroll to <?php echo date( 'F', mktime(0, 0, 0, $month) );?>, <?php echo $year; ?>"><?php echo date( 'F', mktime(0, 0, 0, $month) );?></a>
						</li>
						<?php $j++; ?>
						<?php } ?>
					</ul>
				</li>
			<?php $i++; ?>
			<?php endforeach; ?>
		</ul>
	</aside>
	<div class="column eight" itemscope="" itemtype="http://schema.org/Blog">
		<?php $posts_in_order = []; ?>
		<?php // build the array; ?>
		<?php while (have_posts()) : the_post(); ?>

			<?php ob_start(); ?>
			<?php partial('single', get_post_type()); ?>
			<?php $this_posts_content = ob_get_clean(); ?>

			<?php if ( ! array_key_exists( get_the_date( 'MY' ), $posts_in_order ) ): ?>
				<?php $posts_in_order[get_the_date( 'MY' )] = array(
					'pretty_year' => get_the_date( 'F Y' ),
					'posts' => array()
				); ?>
			<?php endif; ?>
			<?php $posts_in_order[get_the_date( 'MY' )]['posts'][] = array(
				'content' => $this_posts_content,
				); ?>
			<?php endwhile; ?>
			<section class="container bleed">
				<?php foreach($posts_in_order as $year => $years_content): ?>
					<div data-section="<?php echo strtolower(str_replace(' ', '-', $years_content['pretty_year'])); ?>" class="current-year column twelve"><span class="h2"><strong><?php echo $years_content['pretty_year']; ?></strong></span></div>
					<div class="column twelve post-mosiac">
						<div class="the-mosiac">
						<?php foreach($years_content['posts'] as $post): ?>
							<?php echo $post['content']; ?>
						<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</section>
		</div>
	</div>
	<?php sendo()->capture_javascript_start(); ?>
	<script type="text/javascript">

		var rebind_mosiac = function() {
			if ($(window).innerWidth() > 500) {
				$('.the-mosiac', '.post-mosiac').each(function() {
					var side1 = 0,
						side2 = 0;

					$(this).children().each(function(index, element) {
						if ( side1 <= side2 ) {
							$( this ).css( 'top', side1 + 'px' );
							side1 += parseInt( $( this ).css( 'height' ) );
						} else if ( side2 < side1 ) {
							$( this ).css( 'top', side2 + 'px' )
							$( this ).css('left', '50%');
							side2 += parseInt( $( this ).css('height') )
						}

						$( this ).addClass('in');
					});

					var biggest = (side1 > side2) ? side1 : side2;

					$(this).css('height', biggest).parent().prev('.current-year').addClass('in');
				});
			} else {
				$('.the-mosiac', '.post-mosiac').each(function() {
					$(this).removeAttr('style').parent().prev('.current-year').addClass('in');

					$(this).children().each(function(index, element) {
						$( this ).removeAttr('style').addClass('in');
					});
				});
			}
		}

		$(document).ready(function() {

			var mosiac_ensurer = setInterval(rebind_mosiac, 500);

			var lastScrollTop = 0;

			$(window).on('scroll.archive', function() {
				var sidebar_distance_from_top = $('aside').offset().top;
				var footer_distance_from_top = $('footer').offset().top;
				var window_distance = $(window).scrollTop();
				var normalized_distance_from_top = window_distance - sidebar_distance_from_top;

				normalized_distance_from_top = (normalized_distance_from_top <= 0) ? 0 : normalized_distance_from_top;

				if (normalized_distance_from_top > 0) {

					if ((normalized_distance_from_top + ($('aside > ul').innerHeight() * 2)) > (footer_distance_from_top - sidebar_distance_from_top)) {
						mathed_distance = (footer_distance_from_top - sidebar_distance_from_top - ($('aside > ul').innerHeight() * 2));
						$('aside > ul').css('transform', 'matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, ' + mathed_distance  + ', 0, 1)');
					} else {
						$('aside > ul').css('transform', 'matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, ' + normalized_distance_from_top  + ', 0, 1)');
					}

					if (window_distance > lastScrollTop) {
						var mathed_distance = parseInt($('aside > ul').css('margin-top')) - (window_distance - lastScrollTop);

						var bottom_cap = ($('aside > ul').innerHeight() - ($(window).innerHeight() * 0.75)) * -1;

						mathed_distance = (mathed_distance < bottom_cap) ? bottom_cap : mathed_distance;
					} else {
						var mathed_distance = parseInt($('aside > ul').css('margin-top')) - (window_distance - lastScrollTop);

						mathed_distance = (mathed_distance > 0) ? 0 : mathed_distance;
					}

					if ($('aside > ul').innerHeight() > $(window).innerHeight()) {
						$('aside > ul').css( { 'margin-top': mathed_distance } );
					}
				} else {
					$('aside > ul').css( { 'margin-top': 0, 'transform': 'matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1)' } )
				}

				lastScrollTop = window_distance;




				$('[data-scrollto]').each(function() {
					var the_scroll_to_distance = $('[data-section="' + $(this).attr('data-scrollto') + '"]').offset().top - ($('header').innerHeight() + $('.page-navigation').innerHeight());

					if ( the_scroll_to_distance < $(window).scrollTop() ) {
						$('[data-scrollto]').removeClass('active').closest('.year-container').removeClass('active');
						$(this).addClass('active').closest('.year-container').addClass('active');
					}
				});
			});

		$(document).on('pjax:send', function() {
			$(window).off('scroll.archive');
		});

	});
</script>
<?php sendo()->capture_javascript_end(); ?>
