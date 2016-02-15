/*----------------------------------------------
	SKETCH THEME CUSTOM JQUERY HANDLER FILE
------------------------------------------------*/
jQuery.noConflict();
var $j = jQuery.noConflict();

/*===========================================================*/
/*	MENU SCRIPT
/*===========================================================*/		
jQuery(document).ready(function(){
	jQuery('#menu').superfish();
	jQuery('#menu li:has(ul)').each(function(){
		jQuery(this).addClass('has_child').prepend('<span class="this_child"></span>');
	});

	/* HEADER TRIGGER HANDLER */
	jQuery("#header-trigger").click(function(e){
		e.preventDefault();
		var body = jQuery('body');
		if (body.hasClass('display-header'))
		{
			body.removeClass('display-header');
			jQuery('.conx-inner-overlay').fadeOut(200);
		}
		else
		{
			body.addClass('display-header');
			jQuery('.conx-inner-overlay').fadeIn(200);
		}
	});

});
	

/*===========================================================*/
/*	WAYPOINTS MAGIC
/*===========================================================*/	
if ( typeof window['vc_waypoints'] !== 'function' ) {
	function vc_waypoints() {
	if (typeof jQuery.fn.waypoint !== 'undefined') {
		$j('.fade_in_hide').waypoint(function() {
				$j(this).addClass('skt_start_animation');
			}, { offset: '90%' });
			$j('.skt_animate_when_almost_visible').waypoint(function() {
				$j(this).addClass('skt_start_animation');
			}, { offset: '90%' });
		}
	}
}
jQuery(document).ready( function() {
	vc_waypoints();
});

/*===========================================================*/
/*	SOME OTHER ELEMENTS SCRIPT 
/*===========================================================*/	
(function($,sr){

  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function (func, threshold, execAsap) {
      var timeout;

      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if (!execAsap)
                  func.apply(obj, args);
              timeout = null;
          };

          if (timeout)
              clearTimeout(timeout);
          else if (execAsap)
              func.apply(obj, args);

          timeout = setTimeout(delayed, threshold || 100);
      };
  }
  // smartresize 
  jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

//Back to top
jQuery(document).ready( function() {
	jQuery('#back-to-top,#backtop').hide();
	jQuery(window).scroll(function() {
		if (jQuery(this).scrollTop() > 100) {
			jQuery('#back-to-top,#backtop').fadeIn();
        } else {
            jQuery('#back-to-top,#backtop').fadeOut();
        }
    });

	jQuery('#back-to-top,#backtop').click(function(){
	    jQuery('html, body').animate({scrollTop:0}, 'slow');
	});
});

jQuery(document).ready(function($) {
	'use strict';
	jQuery("#skenav .ske-menu, ul.max-menu").niceScroll({
		scrollspeed: 60,
		mousescrollstep: 40,
		cursorwidth: 5,
		cursorborder: 0,
		cursorcolor: '#47494E',
		cursorborderradius: 2,
		autohidemode: false,
		horizrailenabled: false
	});

});