/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referring to this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'icomoon\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-laptop': '&#x21;',
		'icon-desktop': '&#x22;',
		'icon-tablet': '&#x23;',
		'icon-phone': '&#x24;',
		'icon-portfolio': '&#x25;',
		'icon-ideas': '&#x26;',
		'icon-inspirations': '&#x27;',
		'icon-timeline': '&#x28;',
		'icon-facebook': '&#x29;',
		'icon-twitter': '&#x2a;',
		'icon-linkedin': '&#x2c;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
