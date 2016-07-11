<header role="banner">
	<div class="container">
		<div class="column">
			<button type="button" class="ellipsis caf" data-func="open_nav">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="sr">Toggle navigation</span>
			</button>
			<nav class="collapse navbar-collapse" role="navigation">
				<?php
				if (has_nav_menu('primary_navigation')) :
					wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new NEIGHBORHOOD_Nav_Walker(), 'menu_class' => 'nav navbar-nav'));
				endif;
				?>
			</nav>
			<a class="logo subhead<?php pjaxify(); ?>" href="<?php echo esc_url(home_url('/')); ?>"><?php partial('assets', 'logo'); ?></a>
		</div>
	</div>
	<progress id="article-progress" value="0"></progress>
</header>

<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">
	$('li:nth-of-type(2)','.dropdown-menu').hover(function() {
		$(this).parent().parent().addClass('first-active')
	}, function() {
		$(this).parent().parent().removeClass('first-active')
	});

	$('a','.dropdown-menu').focus(function() {
		$(this).parent().parent().addClass('nav-opened');
	});

	$('li:nth-of-type(2)','.dropdown-menu').focus(function() {
		$(this).parent().parent().addClass('first-active')
	});


	$('a:last','.dropdown-menu').blur(function() {
		$(this).parent().parent().removeClass('nav-opened');
	});

	$('li:nth-of-type(2)','.dropdown-menu').blur(function() {
		$(this).parent().parent().removeClass('nav-opened');
		$(this).parent().parent().removeClass('first-active')
	});

	λ.open_nav = function(el) {
		console.log('cool');
		if (!$(el).hasClass('active')) {
			$('body, html').addClass('nav-open');
			$(el).addClass('active');
		} else {
			$('body, html').removeClass('nav-open');
			$(el).removeClass('active');
		}
	};

	λ.open_search = function(el) {
		if (!$(el).hasClass('active')) {
			$('body, html').removeClass('nav-open');
			$(el).addClass('active');
		} else {
			$(el).removeClass('active');
		}
	};

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			$('body, html').removeClass('nav-open search-open');
			$('.hamburger').removeClass('active');
		}
	});
</script>
<?php sendo()->capture_javascript_end(); ?>
