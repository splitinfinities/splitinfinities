/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

 (function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var NEIGHBORHOOD = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages

        λ.rebind_caf();
        λ.rebind_scrollto();
        λ.prevent_widows();
        λ.rebind_figcaptions();
        λ.init_testimonial();
        λ.manage_visible_videos();

        var apng_images = document.querySelectorAll(".apng-image");
        for (var i = 0; i < apng_images.length; i++) APNG.animateImage(apng_images[i]);

        $("body").keydown(function(e) {
          if ($('.hidden-nav').length === 1) {
            if (e.keyCode == 37) {
              $('.hidden-link.next').click();
            } else if (e.keyCode == 39) {
              $('.hidden-link.previous').click();
            }
          }
        });

        if ($('[data-section="' + window.location.hash + '"]').length === 1) {
          var scrolling_animation = setTimeout(function() { scrollTo($('[data-section="' + window.location.hash + '"]').offset().top, 1500); }, 2000);
        }

        if ($(window).scrollTop() <= 45) {
          $('header').removeClass('mini')
        } else {
          $('header').addClass('mini')
        }

        $(window).on('scroll', function() {
          if ($(window).scrollTop() <= 45) {
            $('header').removeClass('mini')
          } else {
            $('header').addClass('mini')
          }

          // var percentage = $(window).scrollTop() / $(document).innerHeight();

          // $('.layer_1').css('transform', 'matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, -' + ( percentage )  + ', 0, 1)');

        });

        λ.toggle_navigation = function() {
          if ($('body').hasClass('navigation-is-open')) {
            $('body').removeClass('navigation-is-open').addClass('navigation-is-transitioning');
            $('html').removeClass('navigation-is-open').addClass('navigation-is-transitioning');

            λ.clean_nav_status = setTimeout(function() {
              $('body').removeClass('navigation-is-transitioning');
              $('html').removeClass('navigation-is-transitioning');
            }, 500);

          } else {
            $('body').addClass('navigation-is-open');
            $('html').addClass('navigation-is-open');
          }
        }

        λ.rebind_magnific();

      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired

        λ.rebind_caf();
        picturefill();
        λ.rebind_responsive_background_images();
        λ.rebind_progressive_images();
        λ.mouse_and_gyro_parallax();
        λ.add_zooming_icon();
        λ.rebind_article_progress();
        λ.rebind_hide_navigation_on_scroll();
        λ.rebind_textarea_autoresize();

        if ($('body').hasClass('new-business')) {
          λ.new_business_page_init();
        }

        setTimeout(function() {
          var our_iOS = /iPhone|iPod/.test(navigator.platform);

          $('.if-it-matters').each(function() {
            if (our_iOS) {
              $(this).attr('src', $(this).attr('data-src'));
            }

            var apng_images = document.querySelectorAll("img.apng-image");
            for (var i = 0; i < apng_images.length; i++) APNG.animateImage(apng_images[i]);
          });
        }, 1000);

        λ.object_fit_fallback();
      }
    },
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = NEIGHBORHOOD;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
