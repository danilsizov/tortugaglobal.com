/**
 * Square Custom JS
 *
 * @package square
 *
 * Distributed under the MIT license - http://opensource.org/licenses/MIT
 */

jQuery(function($){

	$('#sq-bx-slider').bxSlider({
		'pager':false,
		'auto' : true,
		'mode' : 'fade',
		'pause' : 5000,
		'prevText' : '<i class="fa fa-angle-left"></i>',
		'nextText' : '<i class="fa fa-angle-right"></i>'
	});


	$('.sq-testimonial-slider').bxSlider({
		'controls' : false,
		'pager': true,
		'auto' : true,
		'pause' : 5000,
		'mode' : 'fade',
		  onSlideBefore: function($slideElement, oldIndex, newIndex){
		    $($slideElement).find('img').addClass('animated wobble');
		    $($slideElement).find('h3').addClass('animated rotateIn');
		  }
	});

    $(".sq_client_logo_slider").owlCarousel({
      autoPlay: 4000, 
      items : 5,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,3],
      pagination : false
    });

    $(".sq-tab-pane:first").show();
    $(".sq-tab li:first").addClass('sq-active');

    $(".sq-tab li a").click(function(){
    	var tab = $(this).attr('href');
    	$(".sq-tab li").removeClass('sq-active');
    	$(this).parent('li').addClass('sq-active');
    	$(".sq-tab-pane").hide();
    	$(tab).show();
    	return false;
    });

    $(window).scroll(function(){
    	var scrollTop = $(window).scrollTop();
    	if( scrollTop > 0 ){
    		$('#sq-masthead').addClass('sq-scrolled');
    	}else{
    		$('#sq-masthead').removeClass('sq-scrolled');
    	}
    });

});

if(jQuery('body').hasClass('page-template-home-template')){
	new ElastiStack( document.getElementById( 'sq-elasticstack' ), {
		// distDragBack: if the user stops dragging the image in a area that does not exceed [distDragBack]px 
		// for either x or y then the image goes back to the stack 
		distDragBack : 200,
		// distDragMax: if the user drags the image in a area that exceeds [distDragMax]px 
		// for either x or y then the image moves away from the stack 
		distDragMax : 450,
		// callback
		onUpdateStack : function( current ) { return false; }
	} );
}