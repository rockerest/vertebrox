//This is a chainable timeout function written by Ryan Coughlin
// http://www.ryancoughlin.com/2009/01/22/jquery-timeout-function/#1
// -=- Chain a pause between animations.
jQuery.fn.idle = function(time){
return this.each(function(){
var i = $(this);
i.queue(function(){
setTimeout(function(){
i.dequeue();
}, time);
});
});
};