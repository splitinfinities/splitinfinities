λ.object_fit_fallback = function() {
	if ($('html').hasClass('edge')) {
		 $('.preview-image figure:last-of-type').each(function () {
			var $figure = $(this),

			$imgUrl = $figure.find('picture > img');

			/* $imgUrl // Object (the img tag, woo hoo!) */

			/* $imgUrl = $imgUrl.src; // Undefined */
			/* $imgUrl = $imgUrl.prop('src'); // Undefined */
			/* $imgUrl = $imgUrl.attr('src'); // Undefined */

			if ($imgUrl) {
				$figure
				.css('background-image', 'url(' + $imgUrl + ')')
				.addClass('compat-object-fit'); /* add CSS which takes .compat-object-fit and makes the background image cover, then hide all images inside of it */
			}
		});
	}
};
