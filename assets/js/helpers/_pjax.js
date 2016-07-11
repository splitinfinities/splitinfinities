$(document).ready(function() {
	if (typeof $.fn.pjax === "function") {
		$(document).pjax('a.ajax', '#main', {
			maxCacheLength: 0,
			timeout: 4000
		});
	}
});

$(document).on('pjax:send', function() {
	animate_loader(80);

	$('.dropdown-menu').removeClass('nav-opened');

	if ($('.hamburger').hasClass('active')) {
		$('.hamburger').click();
	}

	if ($('.search').hasClass('active')) {
		$('.search').click();
	}
});

$(document).on('pjax:complete', function() {
	ga('send', 'pageview');

	$('meta[replace]', 'head').remove();
	$('meta', '#main').each(function() {
		$(this).detach().appendTo('head');
	});

	$('link[replace]', 'head').remove();
	$('link', '#main').each(function() {
		$(this).detach().appendTo('head');
	});
});

$(document).on('pjax:success', function () {
	animate_loader(100);
});

$(document).on('pjax:end', function() {

	$('html, body').removeClass('zoom-overlay-open');

	$('.modal-container').removeClass('active').addClass('fadeOut');

	$('body').attr( 'class', $('.main').attr( 'data-theclass' ) );

	$('.loading').removeClass('loading');

	$('.fadeOut, .fadeOutScale, .fadeOutLeft, .fadeOutRight').each(function() {
		var el = $(this);

		el.addClass('anim');

		if (el.hasClass('fadeOut')) {
			el.removeClass('fadeOut');
		}

		if (el.hasClass('fadeOutScale')) {
			el.removeClass('fadeOutScale');
		}

		else if (el.hasClass('fadeOutLeft')) {
			el.removeClass('fadeOutLeft');
		}

		else if (el.hasClass('fadeOutRight')) {
			el.removeClass('fadeOutRight');
		}
	});

	picturefill();
	λ.rebind_caf();
	ElementQueries.init();
	λ.rebind_responsive_background_images();
	λ.rebind_progressive_images();
	λ.rebind_magnific();
	λ.rebind_figcaptions();
	λ.init_testimonial();
	λ.rebind_article_progress();
	λ.rebind_hide_navigation_on_scroll();
	λ.prevent_widows();
	λ.manage_visible_videos();

	var our_iOS = /iPhone|iPod/.test(navigator.platform);

	$('.if-it-matters').each(function() {
		if (our_iOS) {
			$(this).attr('src', $(this).attr('data-src'));
		}
	});

	var apng_images = document.querySelectorAll("img.apng-image");
	for (var i = 0; i < apng_images.length; i++) APNG.animateImage(apng_images[i]);

	if ($('body').hasClass('new-business')) {
		λ.new_business_page_init();
	};


    λ.object_fit_fallback();

	animate_loader(100);

})

$(document).on('pjax:click', function(event) {
	animate_loader(10);

	$(event.target).addClass('loading');
	$('body').addClass('loading');

	$('.fadeIn, .fadeInScale, .fadeInUp, .fadeInDown, .fadeInLeft, .fadeInRight').each(function() {
		var el = $(this);

		el.addClass('anim');

		if (el.hasClass('fadeIn')) {
			el.removeClass('fadeIn').addClass('fadeOut');
		}

		if (el.hasClass('fadeInScale')) {
			el.removeClass('fadeInScale').addClass('fadeOutScale');
		}

		else if (el.hasClass('fadeInUp')) {
			el.removeClass('fadeInUp').addClass('fadeOut');
		}

		else if (el.hasClass('fadeInDown')) {
			el.removeClass('fadeInDown').addClass('fadeOut');
		}

		else if (el.hasClass('fadeInLeft')) {
			el.removeClass('fadeInLeft').addClass('fadeOutLeft');
		}

		else if (el.hasClass('fadeInRight')) {
			el.removeClass('fadeInRight').addClass('fadeOutRight');
		}
	});

	$(event.target).addClass('loading');
});

$(document).on('pjax:timeout', function(event) {
	event.preventDefault();
});

var animate_loader = function(percent) {
	$('#loader-bar').css( { 'width': percent + 'vw' } )
	if (percent === 100) {
		setTimeout(function() {
			$('#loader-bar').delay(750).fadeOut(250, function() {
				animate_loader(0)
			});
		}, 250)
	} else {
		$('#loader-bar').fadeIn(0);
	}
}

