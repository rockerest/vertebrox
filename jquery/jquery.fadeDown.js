//This wheel reinvented by Tom Randolph
// -=- Custom animation to toggle both sliding open/closed and fading in/out
(function($) {
$.fn.fadeDown = function( speed, easing, callback){return this.animate({opacity: 'toggle',height: 'toggle'},speed,easing,callback);};
})(jQuery);
