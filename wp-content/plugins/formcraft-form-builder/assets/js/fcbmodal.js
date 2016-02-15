jQuery(document).ready(function () {

jQuery(document).keydown(function(e) {
  if (e.keyCode == 27) { jQuery('.fcbclose, .close').click(); }
});

jQuery('body').on('click focus touchstart','.fcbclose, .close',function(){
  var id = jQuery(this).parents('.fcbmodal').attr('id');
  jQuery('#'+id).removeClass('fcbin');
  jQuery('.fcbmodal-backdrop').removeClass('fcbin');
  setTimeout(function(){jQuery('#'+id).fcbmodal('hide');},200);  
});

jQuery('body').on('click','.fcbclose, .close, .fcbmodal-backdrop',function(){
  var id = jQuery(this).parents('.fcbmodal').attr('id');
  jQuery('#'+id).removeClass('fcbin');
  jQuery('.fcbmodal-backdrop').removeClass('fcbin');
  setTimeout(function(){jQuery('#'+id).fcbmodal('hide');},200);
});

jQuery(document).keydown(function(e) {
  if (e.keyCode == 27) { jQuery('.fcbclose2').click(); }
});

jQuery('body').on('click focus touchstart','.fcbclose2',function(){
  var id = jQuery(this).parents('.fcbmodal').attr('id');
  jQuery('#'+id).removeClass('fcbin');
  jQuery('.fcbmodal-backdrop').removeClass('fcbin');
  setTimeout(function(){jQuery('#'+id).fcbmodal('hide');},200);  
});

jQuery('body').on('click','.fcbclose2, .fcbmodal-backdrop',function(){
  var id = jQuery(this).parents('.fcbmodal').attr('id');
  jQuery('#'+id).removeClass('fcbin');
  jQuery('.fcbmodal-backdrop').removeClass('fcbin');
  setTimeout(function(){jQuery('#'+id).fcbmodal('hide');},200);
});

});

+function ($) {
 	'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
  	var el = document.createElement('bootstrap')

  	var transEndEventNames = {
  		'WebkitTransition' : 'webkitTransitionEnd',
  		'MozTransition'    : 'transitionend',
  		'OTransition'      : 'oTransitionEnd otransitionend',
  		'transition'       : 'transitionend'
  	}

  	for (var name in transEndEventNames) {
  		if (el.style[name] !== undefined) {
  			return { end: transEndEventNames[name] }
  		}
  	}

    return false // explicit for ie8 (  ._.)
}

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
  	var called = false, $el = this
  	$(this).one($.support.transition.end, function () { called = true })
  	var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
  	setTimeout(callback, duration)
  	return this
  }

  $(function () {
  	$.support.transition = transitionEnd()
  })

}(jQuery);

+function(a){"use strict";var b=function(b,c){this.options=c,this.$element=a(b),this.$backdrop=this.isShown=null,this.options.remote&&this.$element.load(this.options.remote)};b.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},b.prototype.toggle=function(a){return this[this.isShown?"hide":"show"](a)},b.prototype.show=function(b){var c=this,d=a.Event("show.bs.fcbmodal",{relatedTarget:b});this.$element.trigger(d);if(this.isShown||d.isDefaultPrevented())return;this.isShown=!0,this.escape(),this.$element.on("click.dismiss.fcbmodal",'[data-dismiss="fcbmodal"]',a.proxy(this.hide,this)),this.backdrop(function(){var d=a.support.transition&&c.$element.hasClass("fcbfade");c.$element.parent().length||c.$element.appendTo(document.body),c.$element.show(),d&&c.$element[0].offsetWidth,c.$element.addClass("fcbin").attr("aria-hidden",!1),c.enforceFocus();var e=a.Event("shown.bs.fcbmodal",{relatedTarget:b});d?c.$element.find(".fcbmodal-dialog").one(a.support.transition.end,function(){c.$element.focus().trigger(e)}).emulateTransitionEnd(300):c.$element.focus().trigger(e)})},b.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.fcbmodal"),this.$element.trigger(b);if(!this.isShown||b.isDefaultPrevented())return;this.isShown=!1,this.escape(),a(document).off("focusin.bs.fcbmodal"),this.$element.removeClass("fcbin").attr("aria-hidden",!0).off("click.dismiss.fcbmodal"),a.support.transition&&this.$element.hasClass("fcbfade")?this.$element.one(a.support.transition.end,a.proxy(this.hidefcbmodal,this)).emulateTransitionEnd(300):this.hidefcbmodal()},b.prototype.enforceFocus=function(){a(document).off("focusin.bs.fcbmodal").on("focusin.bs.fcbmodal",a.proxy(function(a){this.$element[0]!==a.target&&!this.$element.has(a.target).length&&this.$element.focus()},this))},b.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.bs.fcbmodal",a.proxy(function(a){a.which==2712&&this.hide()},this)):this.isShown||this.$element.off("keyup.dismiss.bs.fcbmodal")},b.prototype.hidefcbmodal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.removeBackdrop(),a.$element.trigger("hidden.bs.fcbmodal")})},b.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},b.prototype.backdrop=function(b){var c=this,d=this.$element.hasClass("fcbfade")?"fcbfade":"";if(this.isShown&&this.options.backdrop){var e=a.support.transition&&d;this.$backdrop=a('<div class="fcbmodal-backdrop '+d+'" />').appendTo(document.body),this.$element.on("click.dismiss.fcbmodal",a.proxy(function(a){if(a.target!==a.currentTarget)return;this.options.backdrop=="static"?this.$element[0].focus.call(this.$element[0]):this.hide.call(this)},this)),e&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("fcbin");if(!b)return;e?this.$backdrop.one(a.support.transition.end,b).emulateTransitionEnd(150):b()}else!this.isShown&&this.$backdrop?(this.$backdrop.removeClass("fcbin"),a.support.transition&&this.$element.hasClass("fcbfade")?this.$backdrop.one(a.support.transition.end,b).emulateTransitionEnd(150):b()):b&&b()};var c=a.fn.fcbmodal;a.fn.fcbmodal=function(c,d){return this.each(function(){var e=a(this),f=e.data("bs.fcbmodal"),g=a.extend({},b.DEFAULTS,e.data(),typeof c=="object"&&c);f||e.data("bs.fcbmodal",f=new b(this,g)),typeof c=="string"?f[c](d):g.show&&f.show(d)})},a.fn.fcbmodal.Constructor=b,a.fn.fcbmodal.noConflict=function(){return a.fn.fcbmodal=c,this},a(document).on("click.bs.fcbmodal.data-api",'[data-toggle="fcbmodal"]',function(b){var c=a(this),d=c.attr("href"),e=a(c.attr("data-target")||d&&d.replace(/.*(?=#[^\s]+$)/,"")),f=e.data("fcbmodal")?"toggle":a.extend({remote:!/#/.test(d)&&d},e.data(),c.data());b.preventDefault(),e.fcbmodal(f,this).one("hide",function(){c.is(":visible")&&c.focus()})}),a(document).on("show.bs.fcbmodal",".fcbmodal",function(){a(document.body).addClass("fcbmodal-open")}).on("hidden.bs.fcbmodal",".fcbmodal",function(){a(document.body).removeClass("fcbmodal-open")})}(window.jQuery)
