<?php global $nav_items; ?>
<section class="container bleed page-navigation">
	<ul>
	<?php foreach($nav_items as $nav_item): ?>
		<li><a href="#" class="scrollto" data-scrollto="<?php echo $nav_item['section']; ?>"><?php echo $nav_item['name']; ?></a></li>
	<?php endforeach; ?>
	</ul>
</section>

<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">

	var page_paginated_distance = [],
		scrollToNewSection;

	var update_page_paginated_variable = function () {
		$('li', '.page-navigation ul').each(function(i) {
			page_paginated_distance[i] = $(this).offset().left - ( $(window).innerWidth() * 0.05 );
		});
	};

	update_page_paginated_variable();

	$(window).on('resize', function() {
		$('.page-navigation').animate({
			scrollLeft: 0
		}, 0);

		update_page_paginated_variable();
	});

	/* Function responsible for adding the sticky class */
	$(window).scroll(function() {
		if ($(window).scrollTop() >= ( $('section:first').innerHeight() - $('header').innerHeight() ) ) {
			$('.page-navigation').addClass('sticky');
		} else {
			$('.page-navigation').removeClass('sticky');
		}
	});

	/* Function responsible for adding active classes */
	$(window).scroll(function() {
		clearTimeout(window.scrollToNewSection);
		var sections = $('[data-section]');

		sections.each(function(i) {
			if ( $(this).offset().top <= ( $( window ).scrollTop() + ($('header').innerHeight() + $('.page-navigation').innerHeight()) ) ) {
				$('[data-scrollto="' + $(this).attr('data-section') + '"]').parent().addClass('active').siblings().removeClass('active');
			}
		});

		window.scrollToNewSection = setTimeout(function() {
			$('.page-navigation').animate({
				scrollLeft: page_paginated_distance[$('li.active', '.page-navigation').index()]
			}, 300);
		}, 100);

	});
</script>
<?php sendo()->capture_javascript_end(); ?>
