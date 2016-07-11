// Use λ for the placeholder

window.λ = function lambda() {
	this.version = "1";
};

λ.acceleration_updates = function(x, y, newx, newy, event) {
	$('.subtle-parallax').each(function() {

		var dampening = ($(this).attr('data-parallax-dampening')) ? $(this).attr('data-parallax-dampening') : 1;
		var type = ($(this).attr('data-parallax-type') === "abs") ? 'px' : '%' ;

		if ($(this).attr('data-parallax-dir') !== "reverse")
			$(this).css("transform", "translateX( " + ( ( x / dampening ) / 100 ) + type + " )  translateY( " + ( ( y / dampening ) / 100 ) + type +" )");
		else
			$(this).css("transform", "translateX( " + (( ( x / dampening ) / 100 ) * -1) + type +" )  translateY( " + (( ( y / dampening ) / 100 ) * -1) + type +" )");
	});

	$('.parallax-background').css("transform", "perspective(300px) rotateY( " + (x / 600) + "deg )  rotateX( " + (y / 200) + "deg ) translateZ(-100px) scale(1.5)");

	$('.event-background').css("transform", "perspective(300px) rotateY( " + (x / 100) + "deg )  rotateX( " + (y / 800) + "deg ) translateY(-50%) translateZ(-100px) scale(1.5)");
};

λ.mouse_and_gyro_parallax = function() {
	$(document).ready(function() {
		if (Modernizr.touch) {
			window.ondeviceorientation = function(event) {
				var accelerationX = Math.ceil(event.gamma * 10);
				var accelerationY = Math.ceil(event.beta * 10);
				var x = (accelerationX - $('#center').offset().left) + $(window).scrollLeft();
				var y = (accelerationY - $('#center').offset().top) + $(window).scrollTop();
				var newx = -x>0 ? 0 : x;
				var newy = -y>0 ? 0 : y;

				λ.acceleration_updates(x, y, newx, newy, event);
			}
		}
		else {
			$("html").mousemove(function(event) {
				var x = (event.clientX - $('#center').offset().left) + $(window).scrollLeft();
				var y = (event.clientY - $('#center').offset().top) + $(window).scrollTop();
				var newx = (-x>0) ? -x : x;
				var newy = (-y>0) ? -y : y;
				newx = newx / $(window).innerWidth() * 4000;
				newy = newy / $(window).innerHeight() * 4000;

				λ.acceleration_updates(x, y, newx, newy, event);
			});
		}
	});
};

λ.select_text = function(container_id) {
	if (document.selection) {
		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(container_id));
		range.select();
	} else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(document.getElementById(container_id));
		window.getSelection().addRange(range);
	}
};

λ.play_video = function(el) {
	if (el.paused) {
		el.play();
		$(el).parent().addClass('playing_video');
	} else {
		el.pause();
		$(el).parent().removeClass('playing_video');
	}

	$(el).on('ended.this_video', function() {
		$(el).parent().removeClass('playing_video');

		$(el).off('ended.this_video');
	});
};

λ.page_videos;

λ.manage_visible_videos = function() {
	// $(window).off('scroll.manage_visible_videos');

	// λ.page_videos = $('video');

	// $(window).on('scroll.manage_visible_videos', function() {
	// 	$(λ.page_videos).each(function() {
	// 		if (($(this).offset().top + $(this).innerHeight()) <= $(window).scrollTop()) {
	// 			this.pause();
	// 			$(this).parent().removeClass('playing_video');
	// 		}
	// 	});
	// });
};

λ.init_testimonial = function() {
	if ($('.testimonial-slider').length === 1) {
		$('.testimonial-slider').slick({
			infinite: true,
			speed: 350,
			autoplaySpeed: 5000,
			fade: true,
			cssEase: 'linear',
			adaptiveHeight: true,
			accessibility: false,
			autoplay: true,
			dots: false,
		});
	}
};

λ.screenshot_activate_this = function(el) {
	var parent_el = $(el).parent();
	var section_el = $(el).parent().parent();
	$(el).toggleClass('active');
	section_el.toggleClass('active');

	if (section_el.hasClass('active')) {
		parent_el.attr('data-active', ($(el).index() + 1));
	} else {
		parent_el.removeAttr('data-active');
	}

	var the_place_to_go_to = section_el.offset().top - 100;

	scrollTo(the_place_to_go_to, 300);
};

λ.refresh_page_after_closing_modal = false;

λ.open_modal = function(el) {
	$($(el).attr('data-modal')).addClass('active fadeIn').removeClass('fadeOut');

	$('.modal-off-canvas').on('click.off_canvas_click', function(e) {
		λ.close_modal();
	});

	$('html, body').addClass('zoom-overlay-open');

	$('body').on('keydown.close_modal', function(e) {
		if (e.keyCode === 27) {
			λ.close_modal();
		}
	});

	$('textarea').each(function() {
		λ.textarea_autoresize(this);
	});
};

λ.close_modal = function(el) {
	$('.modal-container').removeClass('fadeIn').addClass('fadeOut');

	if (λ.refresh_page_after_closing_modal) {
		$('a.the_filter_link', '.modal-container').click();
		λ.refresh_page_after_closing_modal = false;
	}

	setTimeout(function() {
		$('.modal-container').removeClass('active');
		$('html, body').removeClass('zoom-overlay-open');
	}, 200);

	$('body').off('keydown.close_modal');
};

λ.rebind_caf = function() {
	$('.caf').each(function() {
		if (typeof $._data($(this)[0], "events") === "undefined") {
			$(this).on('click', function(e) {
				e.preventDefault();
				λ[$(this).attr('data-func')](this);
			});
		}
	});
};

λ.textarea_autoresize = function(el) {
	el.style.height = '0px';     //Reset height, so that it not only grows but also shrinks
	el.style.height = (el.scrollHeight + 2) + 'px';    //Set new height
};

λ.rebind_textarea_autoresize = function() {
	$('textarea').off('keydown.bound_to_textareas, keyup.bound_to_textareas');

	$('textarea').on('keydown.bound_to_textareas, keyup.bound_to_textareas', function () {
		λ.textarea_autoresize(this);
	});

	$('textarea').each(function() {
		λ.textarea_autoresize(this);
	});
}

λ.rebind_magnific = function() {
	if (typeof $.fn.magnificPopup !== "undefined") {
		$('.popup-vimeo').magnificPopup({
			type:'iframe'
		});
	}
};

λ.rebind_scrollto = function() {
	$('.scrollto').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();

		/* Remove the active mobile menu, no matter what */
		$('body, html').removeClass('nav-open search-open');
		$('.hamburger, .search').removeClass('active');

		if ($('[data-section="' + $(this).attr('data-scrollto') + '"]').length === 1) {
			scrollTo( $('[data-section="' + $(this).attr('data-scrollto') + '"]').offset().top - ($('header').innerHeight() + $('.page-navigation').innerHeight() - 2) , 700);
		}
	});
};

λ.prevent_widows = function() {
	$('h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, p', '.kitchensink').each(function() {

		var nbsp_str = '';

		if ($(this).attr('spaces')) {
			var spaces = parseInt($(this).attr('spaces'));
		} else {
			var spaces = 1;
		}

		for ( var i = 0; i < spaces; i++ ) {
			nbsp_str += '&nbsp;';
		}

		if ( ( ! $(this).hasClass('widowed') ) && ( ! $(this).parent().hasClass('widowed'))) {
			var wordArray = $(this).html().split(" ");
			if (wordArray.length > 2) {
				wordArray[wordArray.length-2] += nbsp_str + wordArray[wordArray.length-1];
				wordArray.pop();
				$(this).html(wordArray.join(" "));
				$(this).addClass('widowed', true);
			}
		}
	});
};

λ.adjust_figcaptions = function() {
	if ($(window).innerWidth() >= 1200) {
		$('figcaption').each(function() {
			$(this).css('margin-top', '-'+$(this).innerHeight()+'px');
		});
	} else {
		$('figcaption').each(function() {
			if (typeof $(this).attr('style') !== "undefined")
				$(this).removeAttr('style');
		});
	}
};

λ.rebind_figcaptions = function() {
	λ.adjust_figcaptions();

	$(window).off('resize.adjust_figcaptions');

	$(window).on('resize.adjust_figcaptions', function() {
		λ.adjust_figcaptions();
	});
};

λ.add_zooming_icon = function() {
	$('img[data-action="zoom"]').wrap( "<div class='zoomable-image'></div>" );

	$('[data-action="zoom"]').each(function() {
		if (this.width >= ($(window).width() - 80)) {
			$(this).parent().addClass('🎉');

			if (typeof rebind_3dtouch_image_zoom === "function") {
				rebind_3dtouch_image_zoom(this.parentElement);
			}
		} else {
			$(this).parent().removeClass('🎉');
			if (typeof unbind_3dtouch_image_zoom === "function") {
				unbind_3dtouch_image_zoom(this.parentElement);
			}
		}
	});

	$(window).off('resize.zooming_icon');

	$(window).on('resize.zooming_icon', function() {
		$('[data-action="zoom"]').each(function() {
			if (this.width >= ($(window).width() - 80)) {
				$(this).parent().addClass('🎉');

				if (typeof rebind_3dtouch_image_zoom === "function") {
					rebind_3dtouch_image_zoom(this.parentElement);
				}
			} else {
				$(this).parent().removeClass('🎉');
				if (typeof unbind_3dtouch_image_zoom === "function") {
					unbind_3dtouch_image_zoom(this.parentElement);
				}
			}
		});
	});
};

λ.rebind_article_progress = function() {
	λ.rebind_article_progress_timeout = setTimeout(function() {
		var progressBar = $('progress');
		progressBar.attr('value', 0);
		progressBar.removeClass('done');

		$('header').removeClass('article_complete');

		if ($('body').hasClass('single-blog') || $('body').hasClass('single-work')) {
			var winHeight = $(window).height(),
			docHeight = $('article').height(),
			max, value;

			/* Set the max scrollable area */
			max = docHeight - winHeight * 0.5;
			progressBar.attr('max', max);

			$(document).on('scroll.rebind_article_progress', function(){
				value = $(window).scrollTop();
				progressBar.attr('value', value);

				if (value >= max) {
					progressBar.addClass('done');
					$('header').addClass('article_complete');
				} else {
					progressBar.removeClass('done');
					$('header').removeClass('article_complete');
				}
			});
		} else {
			$(document).off('scroll.rebind_article_progress');
		}
	}, 1000);
};

λ.rebind_responsive_background_images = function() {
	$( '[data-bgimg]' ).each(function() {
		/* make the data usable */
		var source_set = atob($( this ).attr( 'data-bgimg' )),
		initial_element = this;

		var items = JSON.parse( source_set );

		λ.reasses_background_images(items, initial_element);

		$(window).off('resize.reasses_background_images');

		$(window).on('resize.reasses_background_images', function() {
			λ.reasses_background_images(items, initial_element);
		});
	});
};


λ.reasses_background_images = function(items, initial_element) {
	var bg_img = items['default'];

	/***
	 * This each will go test the first entry all the way to the last entry for matches, with each match
	 * overriding the bg_img variable.
	 **/
	$.each( items, function(media_query, image_uri) {
		/* feel free to clone this if you'd like to add new hardcoded names, this one checks for "mobile" */
		media_query = ( media_query === "mobile" ) ? '(max-width: 640px)' : media_query;

		if (Modernizr.mq(media_query)) {
			bg_img = image_uri;
		}
	});

	/* TODO: Cache these images so we don't request them on resize. */
	$(initial_element).css('background-image', 'url("' + bg_img + '")');
};

λ.check_for_progressive_images = function() {
	$('.progressive_image.js-not_loaded').each(function() {
		if ($(this).offset().top <= $(window).scrollTop() + ( $(window).innerHeight() * 1.25 ) ) {
			try {
				$(window).off('scroll.load_progressive_images');
				$(this).removeClass('js-not_loaded');

				var the_whole_element = this;

				var has_zoom = ($(this).hasClass('has_zoom')) ? 'data-action="zoom"' : '';

				var picture_array = JSON.parse(atob($('.js-swap-for-picture', this).attr('data-image_info')));

				var largest_image, source = '';

				$.each(picture_array, function(key, val) {
					source += '<source srcset="' + val + '" media="' + key + '" />';
					largest_image = val;
				});

				var picture_tag = '<picture src="' + largest_image + '">';
				picture_tag += source;

				picture_tag += '<img srcset="' + largest_image + '" alt="…" ' + has_zoom + ' />';
				picture_tag += '</picture>';

				$('.js-swap-for-picture', this).replaceWith(picture_tag);

				$('picture img', this).bind('load', function() {
					$(the_whole_element).addClass('totally_loaded');
				});

				λ.rebind_progressive_images();

			} catch(error) {
				λ.rebind_progressive_images();
			}
		}
	});
};

λ.rebind_progressive_images = function() {
	λ.check_for_progressive_images();

	$(window).on('scroll.load_progressive_images', function() {
		λ.check_for_progressive_images();
	});
};

λ.hide_scroll_params = {
	'did_scroll' : null,
	'last_scroll_top' : null,
	'delta' : null,
	'navbar_height' : null,
	'interval': null
};

λ.rebind_hide_navigation_on_scroll = function() {

	$('header').removeClass('nav-up nav-down');

	clearInterval(λ.hide_scroll_params.interval);

	λ.hide_scroll_params = {
		'did_scroll' : null,
		'last_scroll_top' : null,
		'delta' : null,
		'navbar_height' : null,
		'interval': null
	};

	if ($('body').hasClass('single-blog') || $('body').hasClass('single-work')) {
		// Hide Header on on scroll down
		λ.hide_scroll_params.did_scroll;
		λ.hide_scroll_params.last_scroll_top = 0;
		λ.hide_scroll_params.delta = 5;
		λ.hide_scroll_params.navbar_height = $('header').outerHeight();

		$(window).scroll(function(event){
			λ.hide_scroll_params.did_scroll = true;
		});

		λ.hide_scroll_params.interval = setInterval(function() {
			if (λ.hide_scroll_params.did_scroll) {
				hasScrolled();
				λ.hide_scroll_params.did_scroll = false;
			}
		}, 100);

		function hasScrolled() {
			var st = $(this).scrollTop();

			// Make sure they scroll more than delta
			if(Math.abs(λ.hide_scroll_params.last_scroll_top - st) <= λ.hide_scroll_params.delta)
				return;

			// If they scrolled down and are past the navbar, add class .nav-up.
			// This is necessary so you never see what is "behind" the navbar.
			if (st > λ.hide_scroll_params.last_scroll_top && st > λ.hide_scroll_params.navbar_height){
				// Scroll Down
				$('header').removeClass('nav-down').addClass('nav-up');
			} else {
				// Scroll Up
				if(st + $(window).height() < $(document).height()) {
					$('header').removeClass('nav-up').addClass('nav-down');
				}
			}

			λ.hide_scroll_params.last_scroll_top = st;
		}
	} else {
		$('header').removeClass('nav-up nav-down');
		clearInterval(λ.hide_scroll_params.interval);
		λ.hide_scroll_params = {
			'did_scroll' : null,
			'last_scroll_top' : null,
			'delta' : null,
			'navbar_height' : null,
			'interval': null
		};
	}
};

function scrollTo(e,d) {
	var page = $("html, body");

	page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function() {
		page.stop();
	});

	page.animate({ scrollTop: e }, d, function() {
		page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
	});

	return false;
}

function escape_reg_exp(string) {
	return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replace_all(string, find, replace) {
	return string.replace(new RegExp(escape_reg_exp(find), 'g'), replace);
}
