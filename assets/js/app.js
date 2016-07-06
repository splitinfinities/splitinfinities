/**
 * Main application file
 */

'use strict';

var slick = require('./helpers/_slick.js');
var element_queries = require('./helpers/_ElementQueries.js');
var image_zoom = require('./helpers/_image-zoom.js');
var pointer_events = require('./helpers/_pointer-events.js');
var picturefill = require('./helpers/_picturefill.js');
var apng_canvas = require('./helpers/_apng-canvas.js');
var function_api = require('./helpers/_function_api.js');
var pjax = require('./helpers/_pjax.js');
var browser_fixes = require('./helpers/_browser-fixes.js');
var router = require('./helpers/_router.js');


// Select
(function() {
    [].slice.call( document.querySelectorAll( 'select' ) ).forEach( function(el) {
        el.className = el.className + " cs-select";

        new SelectFx(el, {
            newTab : false,
            stickyPlaceholder: false,
            onChange: function(val, el) {
                $('select').trigger('change')
            }
        });
    });
})();
