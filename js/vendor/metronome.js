var getParameterByName = function (e){e=e.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");var t=new RegExp("[\\?&]"+e+"=([^&#]*)"),n=t.exec(location.search);return n==null?"":decodeURIComponent(n[1].replace(/\+/g," "))},
accelerationUpdates = function (x, y, newx, newy, event) { $('.subtle-parallax').css("transform", "translate("+ ( ( x / 60 ) * -1) +"%, "+ ( ( y / 30 ) * -1) +"%)"); }
clickActivate = function(context) { $('.click-activate', context).on('click', function(e){ e.preventDefault(); $($(this).attr('data-clean')).removeClass('active'); $($(this).attr('data-activate')).addClass('active'); $(this).addClass('active'); });},
clickToggle = function(context) { $('.click-toggle', context).on('click', function(e){ e.preventDefault(); $($(this).attr('data-clean')).removeClass('active'); $($(this).attr('data-activate')).toggleClass('active'); $(this).toggleClass('active'); });}

$(document).ready(function() {

	if (getParameterByName('trigger')) {
		setTimeout(function() { $('[data-activate=#'+getParameterByName('trigger')+']').trigger('click').addClass('active').parent().addClass('active').siblings().removeClass('active'); }, 250)
	}

	$('.scroll-to').click(function(e){ e.preventDefault(); $("html, body").animate({ scrollTop: $($(this).attr('data-href')).offset().top }, 500); });

	clickActivate('body');
	clickToggle('body');

	$(document).pjax('.pjax', '#pjax-container');

	$(document).on('pjax:timeout', function(event) {
		// Prevent default timeout redirection behavior
		event.preventDefault();
	});

	$(document).on('pjax:complete', function(event) {
		$('[data-activate=#'+$('.panel', '#pjax-container').attr('id')+']').addClass('active').parent().addClass('active').siblings().removeClass('active');
		clickActivate('#'+$('.panel', '#pjax-container').attr('id'));
		clickToggle('#'+$('.panel', '#pjax-container').attr('id'));

		$("html, body").animate({ scrollTop: $($('#pjax-container')).offset().top - 50 }, 500);

	});

	if (Modernizr.touch) {
		window.ondeviceorientation = function(event) {
			var accelerationX = Math.ceil(event.gamma * 10);
			var accelerationY = Math.ceil(event.beta * 10);
			var x = (accelerationX - $('#center').offset().left) + $(window).scrollLeft();
			var y = (accelerationY - $('#center').offset().top) + $(window).scrollTop();
			var newx = -x>0 ? 0 : x;
			var newy = -y>0 ? 0 : y;

			accelerationUpdates(x, y, newx, newy, event);
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

			accelerationUpdates(x, y, newx, newy, event);
		});
	}

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			// on escape key
		}
	});
});

