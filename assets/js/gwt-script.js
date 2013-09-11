( function( $ ) {
	jQuery(document).ready(function($) {
		$(".hide-widget-toggle").click(function() {
			$(".widget-toggle").slideToggle();
			$(this).toggleClass("show-widget-toggle");
			return false;
		});
	});
} )( jQuery );