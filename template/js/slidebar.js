$ = jQuery;

var slide_wrp     = ".side-menu-wrapper"; // Menu Wrapper
var open_button   = ".menu-open"; // Menu Open Button
var close_button  = ".menu-close"; // Menu Close Button
var overlay       = ".menu-overlay"; // Overlay
var parent_menu   = slide_wrp + " > ul > li > a.parent"

$(document).ready(function() {

	$(slide_wrp).hide().css( {"left": -$(slide_wrp).outerWidth()+'px'}).delay(50).queue(function(){$(slide_wrp).show()});

	$(open_button).click(function(e){
		e.preventDefault();
		$(slide_wrp).css( {"left": "0px"});
		setTimeout(function(){
			$(slide_wrp).addClass('active');
		},50);
		$(overlay).css({"opacity":"1", "width":"100%"});
	});

	$(close_button).click(function(e){
		e.preventDefault();
		$(slide_wrp).css( {"left": -$(slide_wrp).outerWidth()+'px'});
		setTimeout(function(){
			$(slide_wrp).removeClass('active');
		},50);
		$(overlay).css({"opacity":"0", "width":"0"});
	});

	$(document).on('click', function(e) {
		if (!e.target.closest(slide_wrp) && $(slide_wrp).hasClass("active")){
			$(slide_wrp).css( {"left": -$(slide_wrp).outerWidth()+'px'}).removeClass('active');
			$(overlay).css({"opacity":"0", "width":"0"});
		}
	});

	$(document).on('click', parent_menu, function(e) {
		var submenu = $("a.submenu." + e.target.classList[1]);

		if (submenu.is(':visible'))
			submenu.css('display','none');
		else
			submenu.css('display','block');
	});

});
