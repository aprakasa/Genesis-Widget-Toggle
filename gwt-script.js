jQuery(document).ready(function($) {
	$(".hide-widget-toggle").click(function() {
		$("#gwt-widget-toggle").slideToggle();
		$(this).toggleClass("show-widget-toggle");
		return false;
	});
});